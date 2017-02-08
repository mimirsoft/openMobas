<?php
include("../../../framework/framework_masterinclude.php");
require_once("purchasing_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


$ACTION =      '';
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
	$ACTION = $_POST['ACTION'];
	$vendor_id = $_POST['vendor_id'];
}
switch($ACTION)
{
	case "Create Purchase Request":
		$vendor_info = CV_Main::get_cv_from_id($vendor_id);
		$contacts = CV_Main::getall_cv_contacts_from_id($vendor_id);
		$revenue_accounts=Purchasing_System::getall_expense_accounts();
		$clearing_accounts=Purchasing_System::getall_clearing_accounts();
		include("purchasing_rcreate.phtml");
	break;

}


?>
