<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../../../framework/framework_masterinclude.php");
require_once("purchasing_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


$ACTION =      '';
$WARNING['show'] = false;

$vendors = CV_Main::getall_vendors_with_items_purchases_allowed();

$invoices = Purchase_Requisition::getall_unapprovedrequisitions();
//$unpaid_invoices = Purchase_Requisition::getall_approvedrequisitions();
$approved_no_po = Purchase_Requisition::getall_approvedrequisitions_without_po();
$approved_with_po = Purchase_Requisition::getall_approvedrequisitions_with_po();

$unpaid_purchase = Purchase_Order::getall_unpaidpurchaseorders();
$recurring_purchase = array();



include("purchasing_main.phtml");

?>
