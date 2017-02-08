<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


$ACTION =      '';
$WARNING['show'] = false;
$results = Array();
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = "01";

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$check_amount = $_POST['check_amount'];
    @$comment = $_POST['comment'];
    @$check_no = $_POST['check_no'];
    @$cv_id = $_POST['cv_id'];
    @$remit_no = $_POST['remit_no'];
    @$remittance_deposit = $_POST['remittance_deposit'];
    @$receivable_gl_account = $_POST['receivable_gl_account'];
    @$apply_amount = $_POST['apply_amount'];
    @$invoice_id = $_POST['invoice_id'];
}


switch($ACTION)
{
    //if this page is posted to, usually from the cash_receipts page, with a check amount to be recorded along with a cv_id
    case "Record Check":
        $recorddate_year = @$_POST['recorddate_year'];
        $recorddate_month = @$_POST['recorddate_month'];
        $recorddate_day = @$_POST['recorddate_day'];
        $date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;
        $remit_no = Cash_Receipts::create_remittance($check_amount, $date, $cv_id, $remittance_deposit, $comment, $check_no, $USER->GetUserID(), 0);
    break;
    case "Apply Amount":
            Cash_Receipts::apply_remittance($remit_no, $invoice_id, $apply_amount);
    break;
}

$remittance = Cash_Receipts::get_remittance($remit_no);
$invoices = Invoice::getall_open_invoices_of_customer($remittance['customer_id']);
include("invoices_remittance_advice.phtml");

?>
