<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("clients_include.php");
require_once("../../../framework/classes/CV_Main.class.php");
require_once("../../../framework/classes/Cash_Disbursement.class.php");
require_once("../../../framework/classes/Statement.class.php");
require_once("../../../framework/classes/Invoice.class.php");
require_once("../../../framework/classes/Purchase_Order.class.php");
		
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$cv_id = '';

$WARNING['show'] = false;

$previous_time = strtotime('-1 month',time());

$startdate_year = date('Y', $previous_time);
$startdate_month = date('m', $previous_time);
$startdate_day = date('d', $previous_time);
$enddate_year = date('Y');
$enddate_month = date('m');
$enddate_day = date('d');
$first_only = true;    
$statement_list = Statement::getall_public_statements();

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
}

//get all cv for this user
// then get all data for that cv

$cv_set = CV_Main::getall_cv_for_user($USER->GetUserID());
foreach($cv_set as $cv)
{
	$customer_info = CV_Main::get_cv_from_id($cv['cv_id']);
	if($customer_info['is_customer'])
	{
		$invoices =  Invoice::getall_invoices_of_customer($cv['cv_id']);
		
	}
	if($customer_info['is_vendor'])
	{
		$purchases =  Purchase_Order::getall_purchaseorders_of_vendor($cv['cv_id']);
		$disbursements = Cash_Disbursements::getall_disbursements_to_vendor($cv['cv_id']);
	}
    
	
	$account_array[$customer_info['gl_account_payable']] = $customer_info['GL_AP_name'];
	if($customer_info['gl_account_receivable'] != $customer_info['gl_account_payable'])
	{
		$account_array[$customer_info['gl_account_receivable']] = $customer_info['GL_AR_name'];
	}
	
}
include("clients_main.phtml");

?>
