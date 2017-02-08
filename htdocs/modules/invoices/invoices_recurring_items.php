<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
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
    @$recurringinvoice_id = $_POST['recurringinvoice_id'];
    @$recurringinvoiceitem_id = $_POST['recurringinvoiceitem_id'];
    @$add_item = $_POST['add_item'];
    @$invoiceitem_id = $_POST['invoiceitem_id'];
    @$invoiceitem_count = $_POST['invoiceitem_count'];
    @$invoiceitem_price_per = $_POST['invoiceitem_price_per'];
    @$frequency = $_POST['frequency'];
    $customer_info = CV_Main::get_cv_from_id($customer_id);
    @$invoice_description = $_POST['invoice_description'];
    @$end_date_year = $_POST['end_date_year'];
    @$end_date_month = $_POST['end_date_month'];
    @$end_date_day = $_POST['end_date_day'];
    @$start_date_year = $_POST['start_date_year'];
    @$start_date_month = $_POST['start_date_month'];
    @$start_date_day = $_POST['start_date_day'];
    
}


switch($ACTION)
{
    case "Save Invoice and Add Items":
        $recurringinvoice_id = Recurring_Invoice::create_invoice("NULL", $cr_num, NULL, $frequency, 0.00, 0.00, 0.00, $billto['careof'], $billto['address1'], $billto['address2'], $billto['city'], $billto['state'], $billto['zip'], $shipto['careof'], $shipto['address1'], $shipto['address2'], $shipto['city'], $shipto['state'], $shipto['zip'], $customer_id, $customer_info['gl_account_receivable'], $revenue_account);
        Recurring_Invoice::update_description($recurringinvoice_id, $invoice_description);
        $start_date = $start_date_year."-".$start_date_month."-".$start_date_day;
        Recurring_Invoice::update_start_date($recurringinvoice_id, $start_date);
        $end_date = $end_date_year."-".$end_date_month."-".$end_date_day;
        Recurring_Invoice::update_end_date($recurringinvoice_id, $end_date);
        break;
    case "Update":
        Recurring_Invoice::update_description($recurringinvoice_id, $invoice_description);
        $start_date = $start_date_year."-".$start_date_month."-".$start_date_day;
        Recurring_Invoice::update_start_date($recurringinvoice_id, $start_date);
        $end_date = $end_date_year."-".$end_date_month."-".$end_date_day;
        Recurring_Invoice::update_end_date($recurringinvoice_id, $end_date);
        break;
    case "Add Item":
        $item_info = Inventory_Item::get_inventory_item_from_id($add_item);
        Recurring_Invoice::add_invoiceitem($recurringinvoice_id, $item_info['retail_price'],  1, $item_info['inventory_id']);
    break;
    case "UPDATE ITEM":
        Recurring_Invoice::update_invoiceitem_count($recurringinvoiceitem_id, $invoiceitem_count);
        Recurring_Invoice::update_invoiceitem_price_per($recurringinvoiceitem_id, $invoiceitem_price_per);
    break;
    case "REMOVE ITEM":
        Recurring_Invoice::delete_invoiceitem($recurringinvoiceitem_id);
    break;
    case "DELETE INVOICE":
        try{
            Recurring_Invoice::delete_invoice($recurringinvoice_id);
        }
        catch(InvoiceException $exception)
        {
            if($_SERVER['HTTPS'])
            {
                header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php?WARNING=TRUE&MESSAGE=CANNOTDELETE");
            }
            if(!$_SERVER['HTTPS'])
            {
                header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php?WARNING=TRUE&MESSAGE=CANNOTDELETE");
            }
        }
        if($_SERVER['HTTPS'])
        {
            header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
        }
        if(!$_SERVER['HTTPS'])
        {
            header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
        }
    break;
}

if($recurringinvoice_id == '')
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
$invoice_items = Recurring_Invoice::getall_invoiceitems($recurringinvoice_id);
$items = Inventory_Item::getall_current_items();
$invoice_info = Recurring_Invoice::get_invoice($recurringinvoice_id);

include("invoices_recurring_items.phtml");


?>
