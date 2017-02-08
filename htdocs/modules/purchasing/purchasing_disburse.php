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
    @$po_id_array = $_POST['po_id_array'];
    @$cv_id = $_POST['cv_id'];
    @$vendor_id = $_POST['vendor_id'];
    @$disbursement_account = $_POST['disbursement_account'];
    @$check_info = $_POST['check_info'];
    
}


switch($ACTION)
{
    case "Print and Record Check":
    	//verify that the sum on the check equals the sum of the amounts to be applied
    	$check_total_check = 0;
        foreach($po_id_array as $po_id)
        {
        	$check_total_check += $check_amounts[$po_id];
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
        foreach($po_id_array as $po_id)
        {
			$purchase_info = Purchase_Order::get_purchase_order($po_id);
        	//if amount remaining is not less than the amount we want to apply
        	$remaining = $purchase_info['po_total']-$purchase_info['total_disbursed'];
        	if(bccomp($remaining, $check_amounts[$po_id], 2) < 0 )
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
        foreach($po_id_array as $po_id)
        {
        	$comment_complete .= $comment[$po_id]." ";
        }
        $comment = "CHECK FOR ".$comment_complete;
        //record the check
        $remit_no = Cash_Disbursements::create_disbursement($check_total, $date, $cv_id, $disbursement_account, $comment, $check_info['check_no'], $USER->GetUserID(), 0);
        foreach($po_id_array as $po_id)
        {
        	//apply the check
        	Cash_Disbursements::apply_disbursement($remit_no, $po_id, $check_amounts[$po_id]);
        }
        // this should be moved to a seperate window, that is spawned onload by the javascript
       $WARNING['show'] = true;
       $WARNING['message'] = $comment."totaling $".number_format($check_total, 2)." recorded and applied!";
       $WARNING['message'] .= "<BR/><a href=\"".$BASE_DIR."/purchasing/purchasing_main.php\" >Return to Purchasing</a>";
       $WARNING['message'] .= "<BR/><a href=\"".$BASE_DIR."/purchasing/purchasing_cash_disbursements.php\" >Return to Cash Disburse</a>";
       
    break;
}

$remittance_accounts=Purchasing_System::getall_disbursement_accounts();

$vendor_info = CV_Main::get_cv_from_id($vendor_id);
$unpaid_purchases = Purchase_Order::getall_unpaidpurchaseorders_of_vendor($vendor_id);


include("purchasing_disburse.phtml");

?>
