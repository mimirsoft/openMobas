<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");
require_once("../../../framework/classes/Invoice.class.php");
require_once("../../../framework/classes/Recurring_Invoice.class.php");
require_once("../../../framework/classes/Invoice_System.class.php");
require_once("../../../framework/classes/Inventory_Item.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
if(isset($_GET['ACTION']))
{
    $ACTION = $_GET['ACTION'];
    @$invoice_id = $_GET['invoice_id'];
}    
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$customer_id = $_POST['customer_id'];
    @$account_num = $_POST['account_num'];
    @$revenue_account = $_POST['revenue_account'];
    @$cr_num = $_POST['cr_num'];
    @$billto = $_POST['billto'];
    @$shipto = $_POST['shipto'];
    @$shipto = $_POST['shipto'];
    @$invoice_id = $_POST['invoice_id'];
    @$add_item = $_POST['add_item'];
    @$invoiceitem_id = $_POST['invoiceitem_id'];
    @$invoiceitem_count = $_POST['invoiceitem_count'];
    @$invoiceitem_price_per = $_POST['invoiceitem_price_per'];
    @$invoice_description = $_POST['invoice_description'];
    @$invdate_year = $_POST['invdate_year'];
    @$invdate_month = $_POST['invdate_month'];
    @$invdate_day = $_POST['invdate_day'];
    
}
switch($ACTION)
{
    case "Save Invoice and Add Items":
        $customer_info = CV_Main::get_cv_from_id($customer_id);
    	$invoice_id = Invoice::create_invoice("NULL", $cr_num, 0.00, 0.00, 0.00, $billto['careof'], $billto['address1'], $billto['address2'], $billto['city'], $billto['state'], $billto['zip'], $shipto['careof'], $shipto['address1'], $shipto['address2'], $shipto['city'], $shipto['state'], $shipto['zip'], $customer_id, $customer_info['gl_account_receivable'], $revenue_account);
		Invoice::update_description($invoice_id, $invoice_description);
		$date = $invdate_year."-".$invdate_month."-".$invdate_day;
		Invoice::update_date_charged($invoice_id, $date);
		
    	break;
    case "Add Item":
        $item_info = Inventory_Item::get_inventory_item_from_id($add_item);
        Invoice::add_invoiceitem($invoice_id, $item_info['retail_price'],  1, $item_info['inventory_id']);
    break;
    case "UPDATE ITEM":
        Invoice::update_invoiceitem_count($invoiceitem_id, $invoiceitem_count);
        Invoice::update_invoiceitem_price_per($invoiceitem_id, $invoiceitem_price_per);
    break;
    case "Set To Default Receiveable for Customer":
    	$invoice_info = Invoice::get_invoice($invoice_id);
    	$customer_info = CV_Main::get_cv_from_id($invoice_info['customer_id']);
        Invoice::update_customer_account_id($invoice_id, $customer_info['gl_account_receivable']);
    break;
    case "Update Invoice":
        Invoice::update_invoice($invoice_id, $cr_num, $billto['careof'], $billto['address1'], $billto['address2'], 
        					    $billto['city'], $billto['state'], $billto['zip'], $shipto['careof'], $shipto['address1'],
        					    $shipto['address2'], $shipto['city'], $shipto['state'], $shipto['zip'], $revenue_account);
		Invoice::update_description($invoice_id, $invoice_description);
		$date = $invdate_year."-".$invdate_month."-".$invdate_day;
		Invoice::update_date_charged($invoice_id, $date);
    break;
    case "REMOVE ITEM":
        Invoice::delete_invoiceitem($invoiceitem_id);
    break;
    case "PRINT":
    	//get the requisition
    	require_once("Cash_Disbursement.php");
    	//$invoice_info = Purchase_Requisition::get_purchase_requisition($pr_id);
    	//get all disbursements applied against PO
    	$po_info = Purchase_Order::get_purchase_order($po_id);
    	$invoice_items = Purchase_Requisition::getall_purchaserequest_items($po_info['pr_id']);

    	$disbursements = Cash_Disbursements::getall_disbursements_against_po($po_id);
    	$system_id = 1;
		$statement_object = Framework::get_system($system_id);
		$SYS_INFO = unserialize($statement_object['system_array']);
    	require_once("invoices_print_invoice.css");
    	require_once("invoices_print_invoice.phtml");
    	exit;
    break;
    case "Charge Invoice":
        $date = $invdate_year."-".$invdate_month."-".$invdate_day;
		try{
			Invoice::charge_invoice($invoice_id, $date);
            $invoice_id = '';
        }
        catch(InvoiceException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        }
    break;
    case "Uncharge and Edit":
        try{
			Invoice::uncharge_invoice($invoice_id);
        }
        catch(InvoiceException $exception)
        {
            if($_SERVER['HTTPS'])
            {
                header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php?WARNING=TRUE&MESSAGE=".urlencode($exception->message));
            }
            if(!$_SERVER['HTTPS'])
            {
                header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php?WARNING=TRUE&MESSAGE=".urlencode($exception->message));
            }
            
        }
    break;
    case "DELETE INVOICE":
        try{
            Invoice::delete_invoice($invoice_id);
        	$invoice_id = '';
        }
        catch(InvoiceException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        }
    break;
}

if($invoice_id == '')
{
    if($_SERVER['HTTPS'])
    {
        header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
    }
    if(!$_SERVER['HTTPS'])
    {
        header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
    }
}

$revenue_accounts=Invoice_System::getall_revenue_accounts();

$invoice_items = Invoice::getall_invoiceitems($invoice_id);
$items = Inventory_Item::getall_current_items();
$invoice_info = Invoice::get_invoice($invoice_id);

$contacts = CV_Main::getall_cv_contacts_from_id($invoice_info['customer_id']);

include("invoices_items.phtml");


?>
