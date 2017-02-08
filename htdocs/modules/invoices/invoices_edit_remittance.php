<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


$ACTION =      '';
$WARNING['show'] = false;
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = date('d');

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $remit_no = $_POST['remit_no'];
    @$credit_or_clearing = $_POST['credit_or_clearing'];
    @$check_amounts = $_POST['check_amounts'];
    @$check_total = $_POST['check_total'];
    @$comment = $_POST['comment'];
    @$checkno = $_POST['checkno'];
    @$po_id_array = $_POST['po_id_array'];
    @$cv_id = $_POST['cv_id'];
    @$vendor_id = $_POST['vendor_id'];
    @$disbursement_account = $_POST['disbursement_account'];
	@$cd_date_year = $_POST['cd_date_year'];
    @$cd_date_month = $_POST['cd_date_month'];
    @$cd_date_day = $_POST['cd_date_day'];
            
}
Cash_Receipts::update_total_applied($remit_no);

switch($ACTION)
{
    case "Update":
        $cd_date = $cd_date_year."-".$cd_date_month."-".$cd_date_day;
        try{
            Cash_Receipts::update_remit_date($remit_no, $cd_date);
            Cash_Receipts::update_checkno($remit_no, $checkno);
            Cash_Receipts::update_comment($remit_no, $comment);
            Cash_Receipts::update_credit_or_clearing($remit_no, $credit_or_clearing);
        }
        catch(CashReceiptException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        }
    break;
    case "Unapply Remittance":
        Cash_Receipts::unapply_remittance($remit_no);
    break;
    case "Delete":
        try{
            Cash_Receipts::delete_remittance($remit_no);
        }
        catch(CashReceiptException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        }
            $remit_no = '';
    break;
}
if($remit_no == '')
{
    if($_SERVER['HTTPS'])
    {
        header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_cash_receipts.php");
    }
    if(!$_SERVER['HTTPS'])
    {
        header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_cash_receipts.php");
    }
}

$remit_info = Cash_Receipts::get_remittance($remit_no);
//$remittance_accounts=Purchasing_System::getall_disbursement_accounts();
//$vendor_info = CV_Main::get_cv_from_id($vendor_id);
//$unpaid_purchases = Purchase_Order::getall_unpaidpurchaseorders_of_vendor($vendor_id);


include("invoices_edit_remittance.phtml");

?>
