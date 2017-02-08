<?php
require_once("../../../framework/framework_masterinclude.php");

require_once("purchasing_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);



$ACTION =      '';
$WARNING['show'] = false;
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = date('d');

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $cd_no = $_POST['cd_no'];
    @$credit_or_clearing = $_POST['credit_or_clearing'];
    @$is_refund = $_POST['is_refund'];
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
Cash_Disbursements::update_total_applied($cd_no);

switch($ACTION)
{
    case "Update":
        $cd_date = $cd_date_year."-".$cd_date_month."-".$cd_date_day;
        try{
            Cash_Disbursements::update_cd_date($cd_no, $cd_date);
            Cash_Disbursements::update_checkno($cd_no, $checkno);
            Cash_Disbursements::update_comment($cd_no, $comment);
            Cash_Disbursements::update_credit_or_clearing($cd_no, $credit_or_clearing);
            Cash_Disbursements::updateIsRefund($cd_no, $is_refund);
        }
        catch(CashDisbursementException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        }
    break;
    case "Apply":
        //if its a refund - goto apply refund
        //if its a normal payment, goto apply disbursement normally
        
    break;
    case "Unapply":
        Cash_Disbursements::unapply_disbursement($cd_no);
        
    break;
    case "Delete":
        try{
            Cash_Disbursements::delete_disbursement($cd_no);
        }
        catch(CashDisbursementException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        }
        $cd_no = '';
    break;
}
if($cd_no == '')
{
    if($_SERVER['HTTPS'])
    {
        header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_cash_disbursements.php");
    }
    if(!$_SERVER['HTTPS'])
    {
        header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_cash_disbursements.php");
    }
}
$cd_info = Cash_Disbursements::get_disbursement($cd_no);
//$remittance_accounts=Purchasing_System::getall_disbursement_accounts();
//$vendor_info = CV_Main::get_cv_from_id($vendor_id);
//$unpaid_purchases = Purchase_Order::getall_unpaidpurchaseorders_of_vendor($vendor_id);


include("purchasing_edit_disbursement.phtml");

?>
