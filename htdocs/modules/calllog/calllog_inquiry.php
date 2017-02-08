<?
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include("../../../framework/framework_masterinclude.php");
include("calllog_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "calllog_module"))
{
    echo "Permission Denied to access contacts module.";
    exit;
}
$firstname = '';
$lastname = '';
$phone = '';
$phone2 = '';
$WARNING['show'] = false;
$ACTION = "";
if(isset($_POST['ACTION']))
{
    @$item = $_POST['item'];
    @$ACTION = $_POST['ACTION'];
    @$firstname = $_POST['firstname'];
    @$lastname = $_POST['lastname'];
    @$phone = $_POST['phone'];
    @$phone2 = $_POST['phone2'];
    @$email = $_POST['email'];
    @$contact_notes = $_POST['contact_notes'];
    @$appointment_with = $_POST['appointment_with'];
    @$apt_date_year =  $_POST['apt_date_year'];
    @$apt_date_month = $_POST['apt_date_month'];
    @$apt_date_day =   $_POST['apt_date_day'];
    @$apt_date_hour =   $_POST['apt_date_hour'];
    @$apt_date_minute =   $_POST['apt_date_minute'];
    @$ampm =   $_POST['ampm'];
    if($ampm == "pm")
    {
        $apt_date_hour = $apt_date_hour+12;
    }
    if($apt_date_hour >= 24)
    {
        $apt_date_hour = 23;
    }
}
if(isset($_GET['item']))
{
    @$item = $_GET['item'];
    @$firstname = '';
    @$lastname = '';
    @$phone = '';
    @$phone2 = '';
    @$email = '';
    @$contact_notes = '';
}
$users = Rbac_User::getAllAllowedTo("manage_inventory", "inventory_items");
$items = inventory_manager::getall_available_items();
//get item into
$item_info = inventory_manager::get_inventory_item_from_id($item);
//display info
switch($ACTION)
{
    case "OPEN ITEM":
        
    break;
    case "Record Inquiry":
        //log inquiry
        calllog::log_inquiry($item, $firstname, $lastname, $phone, $phone2, $email, "GENERAL", $contact_notes);
        // send e-mail to ...
        $to=$item_info['cv_default_email'];
        
        // Your subject
        $subject="INQUIRY ON YOUR PROPERTY ";
        
        // From
        $header="from: KMREALTYSD.com <INFO@KMREALTYSD.com>";
        // Your message
        $message="YOU HAVE RECEIVED AN INQUIRY ON YOUR PROPERTY ".$item_info['item_name']."\r\n";
        $message.= " \r\n";
        $message.="THIS IS AN AUTOMATED EMAIL \r\n";
        $message.= " \r\n";
        $message.="YOU DO NOT NEED TO DO ANYTHING, THIS IS FOR YOUR INFORMATION ONLY \r\n";
        $message.= " \r\n";
        $message.= "NAME: ".$firstname." ".$lastname." \r\n";
        $message.= " \r\n";
        // send email
        $sentmail = mail($to,$subject,$message,$header);
        $WARNING['show'] = true;
        $WARNING['message'] = "EMAIL SENT TO: ".$item_info['cv_default_email'];

        $message.= "PHONE: ".$phone." \r\n";
        $message.= " \r\n";
        $message.= "PHONE2: ".$phone2." \r\n";
        $message.= " \r\n";
        $message.= "EMAIL: ".$email." \r\n";
        $message.= " \r\n";
        $message.= $contact_notes;
        $sentmail = mail($item_info['user_email'],$subject,$message,$header);
        $WARNING['message'] .= "<BR />";
        $WARNING['message'] .= "EMAIL SENT TO: ".$item_info['user_email'];


    break;
    case "Schedule Appointment":
        //log inquiry
        $date = mktime ( 3, 0, 0, $apt_date_month, $apt_date_day, $apt_date_year );
        $e_date = date ( "Ymd", $date );
        $e_time = sprintf ( "%02d%02d00, ", $apt_date_hour, $apt_date_minute );
        $description2 = "APPOINTMENT WITH ".$firstname." ".$lastname." REGARDING ".$item_info['item_name']." 1)".$phone." 2)".$phone2;
        $id = calendar::get_next_id();
        calendar::add_entry($id, -1, $USER->GetUserID(), $e_date, $e_time,  date ( "Ymd" ),
            date ( "Gis" ), "15", "2", "P", "E", "APT: ".$item_info['item_name'], $description2);
        $callback = "CALLBACK ADDED for ".$e_date; 
        calendar::add_entry_user($id, $appointment_with, "A", "NULL");
        $log =  $contact_notes." APPOINTMENT SCHEDULED FOR ".$e_date." AT ".$e_time;  
        calllog::log_inquiry($item, $firstname, $lastname, $phone, $phone2, $email, "APPOINTMENT", $log);
        // send e-mail to ...
        $to=$item_info['cv_default_email'];
        
        // Your subject
        $subject="APPOINTMENT SCHEDULED ON YOUR PROPERTY ";
        
        // From
        $header="from: KMREALTYSD.com <INFO@KMREALTYSD.com>";
        
        // Your message
        $message="YOU HAVE RECEIVED AN INQUIRY ON YOUR PROPERTY ".$item_info['item_name']."\r\n";
        $message.= " \r\n";
        $message.="THIS IS AN AUTOMATED EMAIL \r\n";
        $message.= " \r\n";
        $message.="YOU DO NOT NEED TO DO ANYTHING, THIS IS FOR YOUR INFORMATION ONLY \r\n";
        $message.= " \r\n";
        $message.= "NAME: ".$firstname." ".$lastname." \r\n";
        $message.= " \r\n";
        $message.= "APPOINTMENT WAS SET FOR THIS PERSON FOR THIS PROPERTY ON ".$e_date." at ".$e_time." \r\n";
        $message.= $contact_notes;
        // send email
        $sentmail = mail($to,$subject,$message,$header);
        $WARNING['show'] = true;
        $WARNING['message'] = "EMAIL SENT TO: ".$item_info['cv_default_email'];
    break;
    case "Switch Item":
        //log inquiry


    break;
}
include("calllog_inquiry2.phtml");
switch($item_info['item_type'])
{   
    case "property_rental":
        $prop_info = properties::get_property($item_info['native_table_id']);
        include("property_info_template.phtml");
    break;
}

?>
