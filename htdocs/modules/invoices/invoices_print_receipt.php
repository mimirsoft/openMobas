<?php


require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");
require_once("../../../framework/classes/Check.class.php");

$WARNING['show'] = false;

@$check_amount = $_GET['check_amount'];
@$comment = $_GET['comment'];
@$check_no = $_GET['check_no'];
@$disbursement_account = $_GET['disbursement_account'];
@$date = $_GET['date'];
@$cv_id = $_GET['cv_id'];
@$remit_no = $_GET['remit_no'];

//print the check
//we need to move some variable names for the check
$purchase_info = CV_Main::get_cv_from_id($cv_id);

      	
$net = $check_amount;
$receipt_string = checks::check_amount($check_amount);
$careof = '';
$memo = $comment;
$net = number_format($net, 2);
$account_name = "ACCOUNT ".$disbursement_account;
$checkname = $purchase_info['cv_name'];
$checkname2 = $purchase_info['cv_name'];
$address = $purchase_info['cv_default_address'];
$city = $purchase_info['cv_default_city'];
$state = $purchase_info['cv_default_state'];
$zip = $purchase_info['cv_default_zip'];
$checkdate = $date;
include("cash_receipt.css");
include("cash_receipt.phtml");
exit;

?>