<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("purchasing_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
if(isset($_GET['pr_id']))
{
	@$pr_id = $_GET['pr_id'];
}	
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$vendor_id = $_POST['vendor_id'];
    @$account_num = $_POST['account_num'];
    @$expense_account = $_POST['expense_account'];
    @$clearing_account = $_POST['clearing_account'];
    @$pr_id = $_POST['pr_id'];
    @$po_id = $_POST['po_id'];
    @$add_item = $_POST['add_item'];
    @$purchaseitem_id = $_POST['purchaseitem_id'];
    @$purchaseitem_count = $_POST['purchaseitem_count'];
    @$purchaseitem_price_per = $_POST['purchaseitem_price_per'];
    @$purchase_description = $_POST['purchase_description'];
    @$prdate_year = $_POST['prdate_year'];
    @$prdate_month = $_POST['prdate_month'];
    @$prdate_day = $_POST['prdate_day'];
    @$podate_year = $_POST['podate_year'];
    @$podate_month = $_POST['podate_month'];
    @$podate_day = $_POST['podate_day'];
    @$po_discount = $_POST['po_discount'];
    @$pr_discount = $_POST['pr_discount'];
    @$invoice_id = $_POST['invoice_id'];
    
}
$customer_info = CV_Main::get_cv_from_id($vendor_id);

switch($ACTION)
{
    case "Save Purchase Requisition and Add Items":
		$vendor_info = CV_Main::get_cv_from_id($vendor_id);
		if($vendor_info['purchases_allowed'] == 0 )
		{
			echo "NO PURCHASES ALLOWED FOR VENDOR";
			exit;
		}
    	$pr_id = Purchase_Requisition::create_purchase_request("NULL", date("Ymd"), $vendor_id, $expense_account, $vendor_info['GL_AP_id'], $clearing_account);
        Purchase_Requisition::update_pr_requestor($pr_id, $USER->GetUserID());
    	Purchase_Requisition::update_description($pr_id, $purchase_description);
    	break;
    case "Update Info":
        Purchase_Requisition::update_description($pr_id, $purchase_description);
        Purchase_Requisition::update_expense_account($pr_id, $expense_account);
        $date = $prdate_year."-".$prdate_month."-".$prdate_day;
        Purchase_Requisition::update_date($pr_id, $date);
        
        break;
    case "Update Description":
        Purchase_Requisition::update_description($pr_id, $purchase_description);
    break;
    case "Update Discount":
        Purchase_Requisition::update_discount($pr_id, $pr_discount);
        //if approved
        if($po_id != '')
        {
            Purchase_Order::update_discount($po_id, $po_discount);
        }
    break;
    case "Update Dates":
        //need to add PO date handling
        $date = $prdate_year."-".$prdate_month."-".$prdate_day;
        $podate = $podate_year."-".$podate_month."-".$podate_day;
        Purchase_Requisition::update_date($pr_id, $date);
        $po_info = Purchase_Order::get_purchase_order_from_pr($pr_id);
        Purchase_Order::update_date($po_info['po_id'], $podate);
        $po_info = '';
    break;
    case "Update Invoice":
        //need to add PO date handling
        Purchase_Requisition::updateInvoiceID($pr_id, $invoice_id);
        //$po_info = Purchase_Order::get_purchase_order_from_pr($pr_id);
        //Purchase_Order::update_date($po_info['po_id'], $invoice_id);
        //$po_info = '';
    break;
    case "Add Item":
        $item_info = Inventory_Item::get_inventory_item_from_id($add_item);
        Purchase_Requisition::add_pr_item($pr_id, $item_info['retail_price'],  1, $item_info['inventory_id']);
    break;
    case "UPDATE ITEM":
        Purchase_Requisition::update_invoiceitem_count($purchaseitem_id, $purchaseitem_count);
        Purchase_Requisition::update_invoiceitem_price_per($purchaseitem_id, $purchaseitem_price_per);
    break;
    case "REMOVE ITEM":
        Purchase_Requisition::delete_purchaseitem($purchaseitem_id);
    break;
    case "Approve":
    	$purchase_info = Purchase_Requisition::get_purchase_requisition($pr_id);
    	$vendor_info = CV_Main::get_cv_from_id($purchase_info['vendor_id']);
		if($vendor_info['purchases_allowed'] == 0 )
		{
			echo "NO PURCHASES ALLOWED FOR VENDOR";
			exit;
		}
    	//mark requisition approved
    	Purchase_Requisition::approve($pr_id, $USER->GetUserID());
        $pr_id = '';
    break;
    case "Unapprove and Edit":
        Purchase_Requisition::unapplyDisbursementsAgainstPR($pr_id);
    	try{
    		Purchase_Requisition::unapprove($pr_id);
    	}
        catch(PurchaseRequestException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        }
    break;
    case "GENERATE PO":
    	//mark requisition approved
    	Purchase_Requisition::generate_purchaseorder($pr_id);
        $pr_id = '';
    break;
    case "POST TO LEDGER":
    	//mark requisition approved
    	$date = date('Y')."-".date('m')."-".date('d');
    	Purchase_Order::update_ledger_on_order($po_id, $date);
        $pr_id = '';
    break;
    case "PRINT":
    	//get the requisition
    	require_once("Cash_Disbursement.php");
    	//get all disbursements applied against PO
    	$po_info = Purchase_Order::get_purchase_order($po_id);
    	$invoice_items = Purchase_Requisition::getall_purchaserequest_items($po_info['pr_id']);
        $pr_info = Purchase_Requisition::get_purchase_requisition($po_info['pr_id']);
        
    	$disbursements = Cash_Disbursements::getall_disbursements_against_po($po_id);
    	$system_id = 1;
		$statement_object = Framework::get_system($system_id);
		$SYS_INFO = unserialize($statement_object['system_array']);
    	require_once("purchasing_print_po.css");
    	require_once("purchasing_print_po.phtml");
    	exit;
    break;
    case "SUMMARY":
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
    	require_once("purchasing_summary_statement.css");
    	require_once("purchasing_summary_statement.phtml");
    	exit;
    break;
    case "Delete Purchase Requisition":
        try{
            Purchase_Requisition::delete_pr($pr_id);
        }
        catch(InvoiceException $exception)
        {
            if($_SERVER['HTTPS'])
            {
                header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_main.php?WARNING=TRUE&MESSAGE=CANNOTDELETE");
            }
            if(!$_SERVER['HTTPS'])
            {
                header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_main.php?WARNING=TRUE&MESSAGE=CANNOTDELETE");
            }
        }
        if($_SERVER['HTTPS'])
        {
            header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_main.php");
        }
        if(!$_SERVER['HTTPS'])
        {
            header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_main.php");
        }
    break;
}

if($pr_id == '')
{
    if($_SERVER['HTTPS'])
    {
        header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_main.php");
    }
    if(!$_SERVER['HTTPS'])
    {
        header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_main.php");
    }
}

$revenue_accounts=Purchasing_System::getall_expense_accounts();
$invoice_items = Purchase_Requisition::getall_purchaserequest_items($pr_id);
$invoice_info = Purchase_Requisition::get_purchase_requisition($pr_id);
$items = Inventory_Item::getall_current_items_from_vendor($invoice_info['vendor_id']);
$po_info = Purchase_Order::get_purchase_order_from_pr($pr_id);
if(Purchase_Requisition::is_approved($pr_id) == 1)
{
    include("purchasing_view.phtml");
    
}
elseif(Purchase_Requisition::is_approved($pr_id) == 0)
{
include("purchasing_items.phtml");
    
}


?>
