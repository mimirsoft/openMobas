<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("timecard_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$ACTION = '';

if(isset($_POST['ACTION']))
{
   	$startdate_year = $_POST['startdate_year'];
    $startdate_month = $_POST['startdate_month'];
    $startdate_day = $_POST['startdate_day'];
    $enddate_year = $_POST['enddate_year'];
    $enddate_month = $_POST['enddate_month'];
    $enddate_day = $_POST['enddate_day'];
    $pto = $_POST['pto'];
    $comp = $_POST['comp'];
    $mileage = $_POST['mileage'];
    $holiday = $_POST['holiday'];
    $ACTION = $_POST['ACTION'];
}
$startdate = $startdate_year."-".$startdate_month."-".$startdate_day;
$enddate = $enddate_year."-".$enddate_month."-".$enddate_day;
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "timecard_module"))
{
    echo "Permission Denied to access documents module.";
    exit;
}
if(isset($_POST['print_user']))
{
$Timecard = new Timecard($_POST['print_user']);
}
Else
{
$Timecard = new Timecard($USER->GetUserID());
}

@$Timecard_user = new User($Timecard->getUserId());
$Timecard_IP = new Timecard_IP();

if(!$Timecard_IP->checkIP($_SERVER['REMOTE_ADDR']))
{
    echo "Cannot Access Timecard from this IP ".$_SERVER['REMOTE_ADDR'];
    exit;
}

switch($ACTION)
{
    case "Print Card":
    	$stamps = $Timecard->getAllClockInsBetweenDates($startdate, $enddate);
    	$row = $Timecard->sumClockInsBetweenDates($startdate, $enddate);
    	$seconds = $row['seconds'];
    	$hours_worked = $seconds/3600;
    	break;
}
//check if clocked in

include("timecard_print.phtml");

?>

