<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("leads_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION = '';
//$whenreturn_date_year = date('Y');
//$whenreturn_date_month = date('m');
//$whenreturn_date_day = date('d');
//$whenreturn_date_hour = date("h");
//$whenreturn_date_minute = date("i");
$whenreturn_date_year = '';
$whenreturn_date_month = '';
$whenreturn_date_day = '';
$whenreturn_date_hour = '';
$whenreturn_date_minute = '';
$ampm = '';
$callback = '';
$appt_msg = '';
$WARNING['show'] = false;
if(isset($_GET['lead_id']))
{
    @$lead_id = $_GET['lead_id'];
}    
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
if($whenreturn_date_hour == 12)
{
    $whenreturn_date_hour = 0;
}
if($ampm == "pm")
{
    $whenreturn_date_hour = $whenreturn_date_hour+12;
}
if($whenreturn_date_hour >= 24)
{
    $whenreturn_date_hour = 23;
}
$whenreturn_date = $whenreturn_date_year."-".$whenreturn_date_month."-".$whenreturn_date_day." ".$whenreturn_date_hour.":".$whenreturn_date_minute;
switch($ACTION)
{
	case "Add Update":
        lead::add_entry("NULL", $leadupdate_txt, $lead_id, $USER->GetUserID());
        break;
    case "Remove Tag":
     	 lead::delete_tag($leadcat_id, $lead_id);
        break;
    case "Add Tag":
     	try{
             lead::add_tag($leadcat_id, $lead_id);
        }
        catch(LeadException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        } 
        break;
    case "Create Lead":
         $lead_id = lead::create_lead('NULL', $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip, $comments, 
                                        $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address,
                                        $USER->GetUserID(), $whoassigned_id, $whenreturn_date, $description, $leadorigin_id, $prop_unit);
        break;
    case "Save Lead":
        $lead_info = lead::get_lead_from_id($lead_id);
        //if the lead has been assigned to someone else
        if($lead_info['whoassigned_id'] != $whoassigned_id)
        {
            $new_assigned = new User($whoassigned_id, "", "");
            lead::add_entry("NULL", "Lead assigned to ".$new_assigned->GetUserName(), $lead_id, $USER->GetUserID());
        }
        lead::update_lead($lead_id, $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip, $comments, 
                                            $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address,
                                            $USER->GetUserID(), $whoassigned_id, $whenreturn_date, $description, $leadorigin_id, $prop_unit);
        lead::setColor($lead_id, $color);
    break;
    case "Close Lead":
        lead::close_lead($lead_id, $USER->GetUserID(), $whoassigned_id); 
    break;
    case "Reopen Lead":
        lead::open_lead($lead_id, $USER->GetUserID(), $whoassigned_id); 
    break;
    case "Add Callback":
        $date = mktime ( 3, 0, 0, $whenreturn_date_month, $whenreturn_date_day, $whenreturn_date_year );
        $e_date = date ( "Ymd", $date ) . ", ";
        $e_time = sprintf ( "%02d%02d00, ", $whenreturn_date_hour, $whenreturn_date_minute );
        $description2 = "Call Back ".$firstname." ".$lastname." for property ".$prop_street." ".$prop_city." 1)".$phone_num." 2)".$phone_num2;
        $id = calendar::get_next_id();
        calendar::add_entry($id, -1, $USER->GetUserID(), $e_date, $e_time,  date ( "Ymd" ), 
                        date ( "Gis" ), "15", "2", "P", "E", "CALL BACK", $description2);
        $callback = "CALLBACK ADDED for ".$e_date; 
        calendar::add_entry_user($id, $USER->GetUserID(), "A", "NULL");
        lead::update_lead($lead_id, $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip, $comments, 
                                            $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address,
                                            $USER->GetUserID(), $whoassigned_id, $whenreturn_date, $description, $leadorigin_id);
       
        break;
    case "Add Appointment":
        $date = mktime ( 3, 0, 0, $whenreturn_date_month, $whenreturn_date_day, $whenreturn_date_year );
        $e_date = date ( "Ymd", $date ) . ", ";
        $e_time = sprintf ( "%02d%02d00, ", $whenreturn_date_hour, $whenreturn_date_minute );
        $description2 = "Appointment with  ".$firstname." ".$lastname." for property ".$prop_street." ".$prop_city." ".$prop_state." ".$prop_zip." 1)".$phone_num." 2)".$phone_num2." Email ".$email_address;
        $id = calendar::get_next_id();
        calendar::add_entry($id, -1, $USER->GetUserID(), $e_date, $e_time,  date ( "Ymd" ), 
                        date ( "Gis" ), "15", "2", "P", "E", "Appointment", $description2);
        $appt_msg = "APPOINTMENT ADDED for ".$e_date; 
        calendar::add_entry_user($id, $USER->GetUserID(), "A", "NULL");
        lead::update_lead($lead_id, $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip, $comments, 
                                            $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address,
                                            $USER->GetUserID(), $whoassigned_id, $whenreturn_date, $description, $leadorigin_id);
       
        break;
    case "Convert Lead":
        lead::convert_lead($lead_id, $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip, $comments, 
                                            $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address,
                                            $USER->GetUserID(), $whoassigned_id, $description, $prop_unit);
        break;
    default:
}


$users = Rbac_User::getAllAllowedTo("access_module", "leads_module", $FRAMEWORK);
$userArray = $FRAMEWORK->getUserArray();		
$pad = array("user_id"=>0, "username"=>"UNASSIGNED");
array_unshift($users, $pad);                       
$userArray[0] = $pad; 

$lead_info = lead::get_lead_from_id($lead_id);
$lead_tags = lead::get_tags_of_lead($lead_id);
$leadtypes = lead::getall_leadcats();
$leadOrigin = lead::getall_leadorigins();

$str_when_return = strtotime($lead_info['whenreturn_date']);
if($str_when_return == -62169955200)
{
	
}else {
$whenreturn_date_year = date('Y', $str_when_return);
$whenreturn_date_month = date('m', $str_when_return);
$whenreturn_date_day = date('d', $str_when_return);
$whenreturn_date_hour = date('h', $str_when_return);
$whenreturn_date_minute = date('i', $str_when_return);
}

$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT * FROM lead_entry WHERE lead_id=:1: ");
$stmt->execute($lead_id);
$caseentrys = $stmt->fetchall_assoc();

include("leads_update.phtml");