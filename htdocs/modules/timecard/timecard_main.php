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
    $ACTION = $_POST['ACTION'];
}
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "timecard_module"))
{
    echo "Permission Denied to access documents module.";
    exit;
}
$Timecard = new Timecard($USER->GetUserID());
$Timecard_IP = new Timecard_IP();

if(!$Timecard_IP->checkIP($_SERVER['REMOTE_ADDR']))
{
    echo "Cannot Access Timecard from this IP ".$_SERVER['REMOTE_ADDR'];
    exit;
}

switch($ACTION)
{
    case "Clock In":
    	$Timecard->clockIn();        
    	break;
    case "Clock Out":
    	$Timecard->clockOut();        
    	break;
}
//check if clocked in

include("timecard_main.phtml");