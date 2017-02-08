<?
include("../../../framework/framework_masterinclude.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

require_once("../purchasing/Purchase_Requisition.php");

$ACTION =      '';
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$po_id = $_POST['po_id'];

}
switch($ACTION)
{

    case "VIEW/PRINT":
    	//get the requisition
    	require_once("../purchasing/Cash_Disbursement.php");
    	//get all disbursements applied against PO
    	$po_info = Purchase_Order::get_purchase_order($po_id);
    	$invoice_items = Purchase_Requisition::getall_purchaserequest_items($po_info['pr_id']);

    	$disbursements = Cash_Disbursements::getall_disbursements_against_po($po_id);
    	$system_id = 1;
		$statement_object = Framework::get_system($system_id);
		$SYS_INFO = unserialize($statement_object['system_array']);
    	require_once("../purchasing/purchasing_print_po.css");
    	require_once("../purchasing/purchasing_print_po.phtml");
    	exit;
    break;
}


?>
