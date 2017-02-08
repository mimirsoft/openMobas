<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("yearend_include.php");
require_once("../cv_master/CV_Main.class.php");
require_once("../purchasing/Purchase_Order.php");
Framework::authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication
error_reporting(E_ALL);
ini_set('display_errors', '1');

$startdate_year = date('Y')-1;
$startdate_month = 1;
$startdate_day = 1;
$enddate_year = date('Y')-1;
$enddate_month = 12;
$enddate_day = 31;
$splitdate_year = date('Y')-1;
$splitdate_month = 12;
$splitdate_day = 31;
$ACTION = '';


if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$cv_list = $_POST['cv_list'];
    @$splitdate_year = $_POST['splitdate_year'];
    @$splitdate_month = $_POST['splitdate_month'];
    @$splitdate_day = $_POST['splitdate_day'];
    @$print_total = $_POST['print_total'];
}
$startdate = $startdate_year."-".$startdate_month."-".$startdate_day;
$enddate = $enddate_year."-".$enddate_month."-".$enddate_day;
$splitdate = $splitdate_year."-".$splitdate_month."-".$splitdate_day;
$dbh = new DB_Mysql();
    
if ($ACTION == "MAKE STATEMENTS GL" & $enddate != "--")
{
    include("1099.css");
    $count = 0;
    $income = 0;
    $totalreport =0;
    $accountIDtoNameArray = transactions::build_accountIDtoName_array(false);
    $system_id = 1;
    $statement_object = Framework::get_system($system_id);
    $SYS_INFO = unserialize($statement_object['system_array']);
    foreach($cv_list as $cv_id)
    {
      include("yearend_1099.php");
    }
    if(@$print_total == "yes")
    {
        echo "TOTAL OF 1099's printed :".$totalreport;
    }
}
elseif ($ACTION == "MAKE STATEMENTS PO" & $enddate != "--")
{
    include("1099.css");
    $count = 0;
    $income = 0;
    $totalreport =0;
    $accountIDtoNameArray = transactions::build_accountIDtoName_array(false);
    $system_id = 1;
    $statement_object = Framework::get_system($system_id);
    $SYS_INFO = unserialize($statement_object['system_array']);
    foreach($cv_list as $cv_id)
    {
      include("yearend_1099.php");
    }
    if(@$print_total == "yes")
    {
        echo "TOTAL OF 1099's printed :".$totalreport;
    }
}
else{
    //Get all vendors
    
    $vendors = CV_Main::getall_vendors();
    foreach($vendors as $dbRow)
    {
        $dbRow['income'] = statements::retrieve_credits($dbRow['account_id'], $startdate, $splitdate);
        $result = Purchase_Order::sum_purchaseorder_of_vendor_date($dbRow['cv_id'], $splitdate, $enddate);
        //echo $dbRow['income']." ".$result['SUM(po_total)'].$dbRow['cv_name']."<BR />";
        $dbRow['income'] += $result['SUM(po_total)'];
        
        $vendors_1099[]  = $dbRow;
    }
    // then get the sum of all PO's this will not be correct for some
    foreach($vendors as $dbRow)
    {
        $result = Purchase_Order::sum_purchaseorder_of_vendor_date($dbRow['cv_id'], $startdate, $enddate);
        $dbRow['income'] = $result['SUM(po_total)'];
        $vendors_1099po[]  = $dbRow;
    } 
    include("yearend_main.phtml");
}

?>
