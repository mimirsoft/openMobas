<?
include("../../../framework/framework_masterinclude.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

require_once("invoices_include.php");
require_once("Cash_Receipt.php");
require_once("Invoice.php");
require_once("../purchasing/Purchase_Order.php");
require_once("../purchasing/Cash_Disbursement.php");

$ACTION =      '';
$WARNING['show'] = false;
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = date('d');

if(isset($_POST['ACTION']))
{
	$ACTION = $_POST['ACTION'];
	@$invoice_id = $_POST['invoice_id'];
	@$po_id = $_POST['po_id'];
	@$clearing_account = $_POST['clearing_account'];
    @$clearing_amount = $_POST['clearing_amount'];
}


switch($ACTION)
{
	//if this page is posted to, usually from the cash_receipts page, with a check amount to be recorded along with a cv_id
	case "Clear Against PO":
		$date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;

		$invoice_info = Invoice::get_invoice($invoice_id);
		$purchase_info = Purchase_Order::get_purchase_order($po_id);
		//check_total must equal the difference between the amount outstanding on either the Invoice, or the PO
		$po_remaining = $purchase_info['po_total']-$purchase_info['total_disbursed'];
		$invoice_remaining = $invoice_info['invoice_total']-$invoice_info['total_remitted'];
		if($clearing_amount > $invoice_remaining)
		{
			$clearing_amount = $invoice_remaining;
		}
		if($clearing_amount > $po_remaining)
		{
			$clearing_amount = $po_remaining;
		}
		if($clearing_amount == 0)
		{
			if($_SERVER['HTTPS'])
			{
				header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
			}
			if(!$_SERVER['HTTPS'])
			{
				header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
			}
			exit;
		}
		$comment = 'Clearing PO#'.$purchase_info['po_id'].' against Inv#'.$invoice_info['invoice_id'];
		$comment2 = 'Clearing Inv#'.$invoice_info['invoice_id'].' against PO#'.$purchase_info['po_id'];

		$remit_no = Cash_Disbursements::create_disbursement($clearing_amount, $date, $purchase_info['cv_id'], $clearing_account, $comment, '', $USER->GetUserID(), 1);
		Cash_Disbursements::apply_disbursement($remit_no, $po_id, $clearing_amount);

		$remit_no = Cash_Receipts::create_remittance($clearing_amount, $date, $purchase_info['cv_id'], $clearing_account, $comment2, '', $USER->GetUserID(), 1);
		Cash_Receipts::apply_remittance($remit_no, $invoice_id, $clearing_amount);
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

$clearing_accounts=Purchasing_System::getall_clearing_accounts();

$invoice_info = Invoice::get_invoice($invoice_id);
$unpaid_purchases = Purchase_Order::getall_unpaidpurchaseorders_of_vendor($invoice_info['customer_id']);

include("invoices_clearing.phtml");

?>
