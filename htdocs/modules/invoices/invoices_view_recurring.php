<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");
require_once("../../../framework/classes/Invoice.class.php");
require_once("../../../framework/classes/Recurring_Invoice.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


$ACTION =      '';
$WARNING['show'] = false;
$searched_invoices =  array();

/*
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$customer_id = $_POST['customer_id'];
    @$invoice_id = $_POST['invoice_id'];
}
if(isset($_GET['WARNING']))
{
    $WARNING['show'] = $_GET['WARNING'];
    $WARNING['message']= $_GET['MESSAGE'];
}
*/
switch($ACTION)
{
    /*case "Search":
        $searched_invoices= Invoice::getall_invoices_of_customer($customer_id);
    break;      
        case "Search Invoice Number":
        $result = Invoice::get_invoice($invoice_id);
        $searched_invoices[] = $result;
    break;      
            case "Get Broken Invoices":
        $searched_invoices = Invoice::getall_brokeninvoices();
    break;
    */      
}


$recurring_invoices = Recurring_Invoice::getall_invoices();

include("invoices_view_recurring.phtml");

?>
