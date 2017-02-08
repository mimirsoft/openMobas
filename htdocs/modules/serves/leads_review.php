<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("leads_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$WARNING['show'] = false;

$lead_info = '';
$lead_info['firstname'] = '';
$lead_info['lastname'] = '';
$lead_info['phone_num'] = '';
$lead_info['careof'] = '';
$lead_info['street'] = '';
$lead_info['city'] = '';
$lead_info['state'] = '';
$lead_info['zip'] = '';
$lead_info['email_address'] = '';
$ACTION = '';
$VIEW_ALL = 'OPEN'; 
$VIEW_CLOSED = '';
$VIEW_WHOSE = 'OWN';

$SORTBY = '';
$calltime_date_year = date('Y');
$calltime_date_month = date('m');
$calltime_date_day = date('d');
$calltime_date_hour = date("h");
$calltime_date_minute = date("i");
$ampm = '';

$followup_date_year = date('Y');
$followup_date_month = date('m');
$followup_date_day = date('d');
$followup_date_hour = date("h");
$followup_date_minute = date("i");
$ampm = '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_GET->{$key});
}
if($ampm == "pm")
{
    $calltime_date_hour = $calltime_date_hour+12;
}
if($ampm == "pm")
{
    $calltime_date_hour = $calltime_date_hour+12;
}

$calltime_date = $calltime_date_year."-".$calltime_date_month."-".$calltime_date_day." ".$calltime_date_hour.":".$calltime_date_minute.":00";
$followup_date = $followup_date_year."-".$followup_date_month."-".$followup_date_day." ".$followup_date_hour.":".$followup_date_minute.":00";

$calltime_date_hour = date("h");
$followup_date_hour = date("h");

if ($ACTION == "Save Lead")
{
     lead::create_lead('NULL', $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip, $comments, 
                                        $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address,
                                        $USER->GetUserID(), $whoassigned_id, $whenreturn_date, $leadcat_id, $description, $leadorigin_id, $prop_unit);
}

$users = Rbac_User::getAllAllowedTo("access_module", "leads_module", $FRAMEWORK);
$userArray = $FRAMEWORK->getUserArray();		
$pad = array("user_id"=>0, "username"=>"UNASSIGNED");
array_unshift($users, $pad);                       
$userArray[0] = $pad; 

$leadtypes = lead::getall_leadcats();
$leadOrigin = lead::getall_leadorigins();

$settings = lead::getall_settings();
foreach($settings as $row)
{
    $defaults[$row['setting_name']] = $row['setting_value'];
}



include("leads_review.phtml");

?>
