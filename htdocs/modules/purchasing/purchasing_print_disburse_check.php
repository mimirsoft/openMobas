<?php


require_once("../../../framework/framework_masterinclude.php");
require_once("purchasing_include.php");
require_once("../../../framework/classes/Check.class.php");

@$check_total = $_GET['check_total'];
@$comment = $_GET['comment'];
@$check_no = $_GET['check_no'];
@$disbursement_account = $_GET['disbursement_account'];
@$date = $_GET['date'];

@$careof = $_GET['careof'];
@$address = $_GET['address'];
@$checkname = $_GET['checkname'];
@$checkname2 = $_GET['checkname2'];
@$city = $_GET['city'];
@$state = $_GET['state'];
@$zip = $_GET['zip'];
@$checkdate = $_GET['date'];

//print the check

$account_name = "ACCOUNT ".$disbursement_account;
$net = $check_total;
$check_amount = checks::check_amount($check_total);
$memo = $comment;
$net = number_format($net, 2);

$WARNING['show'] = false;
include("../checks/check.css");
include("../checks/checks_body.phtml");
exit;

?>