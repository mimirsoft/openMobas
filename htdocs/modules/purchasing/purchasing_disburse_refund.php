<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once("../../../framework/framework_masterinclude.php");
require_once("purchasing_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


$ACTION =      '';
$WARNING['show'] = false;
$results = Array();
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = date('d');

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$check_amounts = $_POST['check_amounts'];
    @$check_total = $_POST['check_total'];
    @$comment = $_POST['comment'];
    @$check_no = $_POST['check_no'];
    @$remit_no_array = $_POST['remit_no_array'];
    @$cv_id = $_POST['cv_id'];
    @$vendor_id = $_POST['vendor_id'];
    @$disbursement_account = $_POST['disbursement_account'];

    @$remit_no = $_POST['remit_no'];
    @$check_info = $_POST['check_info'];
    
}


switch($ACTION)
{
    case "Print and Record Check":
    	//verify that the sum on the check equals the sum of the amounts to be applied
    	$check_total_check = 0;
        foreach($remit_no_array as $remit_no)
        {
        	$check_total_check += $check_amounts[$remit_no];
        }
        //if it doesn't match, throw an error
        if($check_total_check != $check_total)
        {
        	echo "CHECK AMOUNT DOES NOT EQUAL SUM OF AMOUNTS TO BE APPLIED";
        	exit;
        }
        //check all POs to pay
        // verify all amounts to be disbursed for each PO
        // they must be less than the remaining balances owed
        foreach($remit_no_array as $remit_no)
        {
			$remit_info = Cash_Receipts::get_remittance($remit_no);
        	//if amount remaining is not less than the amount we want to apply
        	$remaining = $remit_info['total_received']-$remit_info['total_applied'];
        	if(bccomp($remaining, $check_amounts[$remit_no], 2) < 0 )
        	{
        		echo "AMOUNT YOU WANT TO APPLY IS GREATER THEN AMOUNT OUTSTANDING DUE ON INVOICE";
        		exit;
        	}
        	
        }
        //build the info of the actual disbursments
        $recorddate_year = @$_POST['recorddate_year'];
        $recorddate_month = @$_POST['recorddate_month'];
        $recorddate_day = @$_POST['recorddate_day'];
        $date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;
        $comment_complete = '';
        foreach($remit_no_array as $remit_no)
        {
        	$comment_complete .= $comment[$remit_no]." ";
        }
        $comment = "CHECK FOR ".$comment_complete;
        //record the check
        try{
            $cd_no = Cash_Disbursements::create_disbursement($check_total, $date, $cv_id, $disbursement_account, $comment, $check_info['check_no'], $USER->GetUserID(), 0);
            Cash_Disbursements::updateIsRefund($cd_no, 1);
            foreach($remit_no_array as $remit_no)
            {
            	//apply the check
            	Cash_Disbursements::apply_refund($cd_no, $remit_no, $check_amounts[$remit_no]);
            }
            // this should be moved to a seperate window, that is spawned onload by the javascript
           $WARNING['show'] = true;
           $WARNING['message'] = $comment."totaling $".number_format($check_total, 2)." recorded and applied!";
        }
        catch(CashDisbursementException $exception)
        {
           $WARNING['show'] = true;
           $WARNING['message'] = $exception->message;
            
        }
        
    break;
}

$remit_info = Cash_Receipts::get_remittance($remit_no);

$disbursement_accounts=Purchasing_System::getall_disbursement_accounts();

$customer_info = CV_Main::get_cv_from_id($remit_info['cv_id']);
$remittances = Cash_Receipts::getAllUnappliedRemittancesOfCustomer($customer_info['cv_id']);
if(count($disbursement_accounts) == 0)
{
   $WARNING['show'] = true;
   $WARNING['message'] = "NO Disbursement Accounts Set Up, Cannot Write Checks";
}
if($customer_info['gl_account_payable'] == '' )
{
   $WARNING['show'] = true;
   $WARNING['message'] = "No Disbursements Allow, Customer Does Not HAve Account Payable Set";
}
if($customer_info['is_vendor'] == 0)
{
   $WARNING['show'] = true;
   $WARNING['message'] = "No Disbursements Allowed, Vendor is set to No";
}
if($customer_info['disbursements_allowed'] == 0)
{
   $WARNING['show'] = true;
   $WARNING['message'] = "No Disbursements Allowed, Disbursements Allowed Set to NO";
}

include("purchasing_disburse_refund.phtml");

?>
