<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("timecard_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


$ACTION = '';
$startdate_year = date('Y', strtotime("-2 weeks"));
$startdate_month = date('m', strtotime("-2 weeks"));
$startdate_day = date('d', strtotime("-2 weeks"));
$enddate_year = date('Y');
$enddate_month = date('m');
$enddate_day = date('d');


if(isset($_POST['ACTION']))
{
   	@$startdate_year = $_POST['startdate_year'];
    @$startdate_month = $_POST['startdate_month'];
    @$startdate_day = $_POST['startdate_day'];
    @$enddate_year = $_POST['enddate_year'];
    @$enddate_month = $_POST['enddate_month'];
    @$enddate_day = $_POST['enddate_day'];
	$ACTION = $_POST['ACTION'];
    @$edit_user = $_POST['edit_user'];
    @$clock_in = $_POST['clock_in'];
    @$clock_out = $_POST['clock_out'];
    @$timecard_id = $_POST['timecard_id'];
}
if($startdate_year == "")
{
    $startdate_year = date('Y', strtotime("-2 weeks"));
    $startdate_month = date('m', strtotime("-2 weeks"));
    $startdate_day = date('d', strtotime("-2 weeks"));
    $enddate_year = date('Y');
    $enddate_month = date('m');
    $enddate_day = date('d');
    
}
$startdate = $startdate_year."-".$startdate_month."-".$startdate_day;
$enddate = $enddate_year."-".$enddate_month."-".$enddate_day;

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "edit_card", "timecard"))
{
    echo "Permission Denied to edit timecards.";
    exit;
}
$Timecard_IP = new Timecard_IP();

if(!$Timecard_IP->checkIP($_SERVER['REMOTE_ADDR']))
{
    echo "Cannot Access Timecard from this IP ".$_SERVER['REMOTE_ADDR'];
    exit;
}

switch($ACTION)
{
    case "Find Clockins":
    	$Timecard = new Timecard($edit_user);
		$clockins = $Timecard->getAllClockInsBetweenDates($startdate, $enddate);
    	
    	
    	break;
    case "SAVE":
    	$Timecard = new Timecard($USER->GetUserID());
    	$Timecard->editCard($timecard_id, $clock_in, $clock_out);
		
    	break;
}
$users = Rbac_User::getAllAllowedTo("access_module", "timecard_module");
//check if clocked in

include("timecard_edit.phtml");

?>
