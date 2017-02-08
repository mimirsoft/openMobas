<?php
require_once("../../../framework/framework_masterinclude.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

require_once("../purchasing/Purchase_Requisition.php");
require_once("../cv_master/Inventory_Item.class.php");
require_once("../cv_master/CV_Main.class.php");
require_once("../purchasing/Cash_Disbursement.php");
require_once("../invoices/Cash_Receipt.php");
require_once("../invoices/Invoice.php");

$ACTION =      '';
$WARNING['show'] = false;
@$cv_id = $_GET['cv_id'];
if(isset($_POST['ACTION']))
{
    @$cv_id = $_POST['cv_id'];
    @$startdate_year = $_POST['startdate_year'];
    @$startdate_month = $_POST['startdate_month'];
    @$startdate_day = $_POST['startdate_day'];
    $startdate = $startdate_year."-".$startdate_month."-".$startdate_day;
    @$enddate_year = $_POST['enddate_year'];
    @$enddate_month = $_POST['enddate_month'];
    @$enddate_day = $_POST['enddate_day'];
    $enddate = $enddate_year."-".$enddate_month."-".$enddate_day;
    @$statementtype = $_POST['statementtype'];
    
}

$system_id = 1;
$statement_object = Framework::get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);

//get the requisition
//$invoice_info = Purchase_Requisition::get_purchase_requisition($pr_id);
//get all disbursements applied against PO
//$po_info = Purchase_Order::get_purchase_order($po_id);
$customer_info = CV_Main::get_cv_from_id($cv_id);
$po_set = Purchase_Order::getall_purchaseorders_of_vendor_between_dates($cv_id, $startdate, $enddate);
$disbursements = array();
foreach($po_set as $po)
{
    $disbursements = array_merge($disbursements, Cash_Disbursements::getall_disbursements_against_po($po['po_id']));
}

//do we want all real disbursement?
$real_disbursements = array();
$real_disbursements = Cash_Disbursements::getall_real_disbursements_to_vendor_between_dates($cv_id, $startdate, $enddate);

$inv_set = Invoice::getall_invoices_of_customer_between_dates($cv_id, $startdate, $enddate);
$payments = array();
foreach($inv_set as $inv)
{
	$payments = array_merge($payments, Cash_Receipts::getall_remittances_against_inv($inv['invoice_id']));
}

require_once("../cv_master/cv_master_statement_summary.css");

switch($statementtype)
{
    case "summary":
        require_once("../cv_master/cv_master_statement_summary.phtml");
    break;
    case "detailed_summary":
        require_once("../cv_master/cv_master_statement_detailedsummary.phtml");
    break;
    case "simplified_outstanding":
        require_once("../cv_master/cv_master_statement_simplifiedoutstanding.phtml");
    break;
    case "itemized_outstanding":
        require_once("../cv_master/cv_master_statement_itemizedoutstanding.phtml");
    break;
    default:
        require_once("../cv_master/cv_master_statement_summary.phtml");
    break;
}        


?>