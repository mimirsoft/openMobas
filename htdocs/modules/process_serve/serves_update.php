<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("serves_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$WARNING['show'] = false;

$ACTION = '';

$serve_date_year = '';
$serve_date_month = '';
$serve_date_day = '';
$serve_date_hour =  '';
$serve_date_minute = '';
$ampm = '';
$complete = 0;

if(isset($_GET['lead_id']))
{
    @$lead_id = $_GET['lead_id'];
}    
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
$serve = new Process_Serve($dbh, $USER, $rbac_user);
if($serve_date_hour == 12)
{
	$serve_date_hour = 0;
}
if($ampm == "pm")
{
	$serve_date_hour = $serve_date_hour+12;
}
if($serve_date_hour >= 24)
{
	$serve_date_hour = 23;
}
$serve_date = $serve_date_year."-".$serve_date_month."-".$serve_date_day." ".$serve_date_hour.":".$serve_date_minute;


switch($ACTION)
{
	/*case "Create Lead":
		 
         $serve_id = $serve->create('NULL', $firstname, $lastname, $comments, 
                                        $street, $city, $state, $zip, $type, $serve_date);
        break;
    */
    case "Save":
        //if the lead has been assigned to someone else
        $serve->update($serve_id, $firstname, $lastname, $comments, $streetnumber,
        $street, $city, $state, $zip, $type, $serve_date, $complete);
        break;
    case "Delete":
            //delete it, and go back to main
        $serve->delete($serve_id);
        header ("Location: serves_main.php");
        break;
        
    default:
}

$serve_info = $serve->getFromId($serve_id);

$str_date = strtotime($serve_info['date']);
if($str_date == -62169955200)
{

}else {
	$serve_date_year = date('Y', $str_date);
	$serve_date_month = date('m', $str_date);
	$serve_date_day = date('d', $str_date);
	$serve_date_hour = date('h', $str_date);
	$serve_date_minute = date('i', $str_date);
}

include("serves_update.phtml");