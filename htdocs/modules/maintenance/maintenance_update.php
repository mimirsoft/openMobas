<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("maintenance_include.php");
require_once("../../../framework/classes/Casesystem.class.php");
require_once("../../../framework/classes/Maintenance.class.php");
require_once("../../../framework/classes/Mailing.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "maintenance_module"))
{
    echo "Permission access Maintenance Module";
    exit;
}

$ACTION = '';
$VIEW_ALL = '';
$VIEW_OPEN = '';
$VIEW_CLOSED = '';
$hiddencasesystem_txt = '';
$WARNING['show'] = false;
$WARNING['message'] = '';
$email_owner_body = '';
$followup_date_year = date('Y');
$followup_date_month = date('m');
$followup_date_day = date('d');
$followup_date_hour = date("h");
$followup_date_minute = date("i");
$ampm = '';

if(isset($_GET['maintenance_id']))
{
    $maintenance_id = $_GET['maintenance_id'];
}
if(isset($_POST['ACTION']))
{
	$ACTION = $_POST['ACTION'];
	@$customer_id = $_POST['customer'];
	@$maintenance_title = $_POST['maintenance_title'];
	@$maintenance_txt = $_POST['maintenance_txt'];
	@$inventory_id = $_POST['inventory_id'];
	@$maintenance_id = $_POST['maintenance_id'];
	@$vendor_id = $_POST['vendor_id'];
	@$email_subject = $_POST['email_subject'];
    @$email_txt = $_POST['email_txt'];
    @$casesystem_main_assigned = $_POST['casesystem_main_assigned'];
    @$case_title = $_POST['case_title'];
    @$casesystem_txt = $_POST['casesystem_txt'];
    @$case_id_to_email = $_POST['case_id_to_email'];
    @$fill_in_to = $_POST['fill_in_to'];
    @$action_needed = $_POST['action_needed'];
    @$cv_id = $_POST['cv_id'];
    @$email_cv_email = $_POST['email_cv_email'];
    @$cv_contact_emails = $_POST['cv_contact_emails'];
    @$followup_date_year = $_POST['followup_date_year'];
    @$followup_date_month = $_POST['followup_date_month'];
    @$followup_date_day = $_POST['followup_date_day'];
    @$followup_date_hour = $_POST['followup_date_hour'];
    @$followup_date_minute = $_POST['followup_date_minute'];
    @$ampm = $_POST['ampm'];
    
    
}


switch($ACTION)
{
    case "Open Maintenance Issue":
	    if($maintenance_title == '')
	    {
	        
	    }
	    else
	    {
	        $item_info  =  Inventory_Item::get_inventory_item_from_id($inventory_id);
	        if($item_info['service_policy'] == 'none')
	        {
	            $maintenance_txt .= "NO MAINTENANCE ALLOWED";
	        }
	        if( $item_info['service_policy'] == 'by_us_with_approval')
	        {
	            $maintenance_txt.= " \r\n";
	            $maintenance_txt .= "VENDOR REQUIRES APPROVAL ON ALL SERVICE";
	            $maintenance_txt.= " \r\n";
	            $maintenance_txt .= "CALL VENDOR FOR CONFIRMATION";
	        }
            if( $item_info['service_policy'] == 'by_vendor')
            {
                $maintenance_txt.= " \r\n";
                $maintenance_txt .= "VENDOR PERFORMS ALL OWN SERVICE";
                $maintenance_txt.= " \r\n";
                $maintenance_txt .= "NO MAINTNANCE ALLOWED";
            }
            if( $item_info['service_policy'] == 'third_party_warranty')
            {
                $maintenance_txt.= " \r\n";
                $maintenance_txt .= "THIRD PARTY WARRANTY - CONTACT FOR SERVICE";
                $maintenance_txt.= " \r\n";
                $maintenance_txt .= "NO MAINTNANCE ALLOWED";
            }
	        $maintenance_id = maintenance::add_maintenance("NULL", $maintenance_title, $maintenance_txt, $USER->GetUserID(), $inventory_id);
            if($customer_id != '' && $customer_id !="none")
            {
                maintenance::update_customer_id($maintenance_id, $customer_id);
            }
	    }
    break;
    case "Save Changes":
        maintenance::update_maintenance($maintenance_id, $maintenance_title, $maintenance_txt, 'NO');
        maintenance::update_action_needed($maintenance_id, $action_needed);
        maintenance::setUpdated($maintenance_id);
        
    break;
    case "Set Follow Up":
        if($followup_date_hour >= 12)
        {
            $followup_date_hour = $followup_date_hour-12;
        }
        if($ampm == "pm")
        {
            $followup_date_hour = $followup_date_hour+12;
        }
        $followup_date = $followup_date_year."-".$followup_date_month."-".$followup_date_day." ".$followup_date_hour.":".$followup_date_minute.":00";
        maintenance::setFollowUp($maintenance_id, $followup_date);
        maintenance::setUpdated($maintenance_id);
        
    break;
    case "Reopen":
        maintenance::reopen_maintenance($maintenance_id);
        break;
    case "Email Vendor":
        $system_id = 1;
        $statement_object = Framework::get_system($system_id);
        $SYS_INFO = unserialize($statement_object['system_array']);
        if($vendor_id == "fill_in")
        {
            mailing::email_stuff_plaintxt($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],$fill_in_to,$email_subject,$email_txt);
            casesystem::add_entry("NULL", "EMAIL SENT TO ".$fill_in_to."\n------------------------------------\n".$email_txt, $hiddencasesystem_txt, $case_id_to_email, $USER->GetUserID());
        }
        else
        {
            $vendor_info = CV_Main::get_cv_from_id($vendor_id);
            mailing::email_stuff_plaintxt($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],$vendor_info['cv_default_email'],$email_subject,$email_txt);
            casesystem::add_entry("NULL", "EMAIL SENT TO ".$vendor_info['cv_default_email']."\n------------------------------------\n".$email_txt, $hiddencasesystem_txt, $case_id_to_email, $USER->GetUserID());
        }
        break;
    case "Email Owner":
        $system_id = 1;
        $statement_object = Framework::get_system($system_id);
        $SYS_INFO = unserialize($statement_object['system_array']);
        $maintenanceinfo = maintenance::get_maintenance($maintenance_id);
        $item_info  =  Inventory_Item::get_inventory_item_from_id($maintenanceinfo['inventory_id']);
        if($email_cv_email)
        {
            mailing::email_stuff_plaintxt($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],"\"".$item_info['cv_name']."\" <".$item_info['cv_default_email'].">",$email_subject,$email_txt);
        }
        foreach($cv_contact_emails as $cv_contact_email)
        {
            $send_to_email = contact::get_email_from_email_id($cv_contact_email);
            mailing::email_stuff_plaintxt($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],"\"".$send_to_email['firstname']." ".$send_to_email['lastname']."\" <".$send_to_email['email_address'].">",$email_subject,$email_txt);
        }
        casesystem::add_entry("NULL", "EMAIL SENT TO ".$item_info['cv_name']."&lt;".$item_info['cv_default_email']."&gt;"."\n------------------------------------\n".$email_txt, $hiddencasesystem_txt, $case_id_to_email, $USER->GetUserID());
        maintenance::setUpdated($maintenance_id);
        
    break;
    case "Email Tenant":
        $system_id = 1;
        $statement_object = Framework::get_system($system_id);
        $SYS_INFO = unserialize($statement_object['system_array']);
        $maintenanceinfo = maintenance::get_maintenance($maintenance_id);
        $customer_info = CV_Main::get_cv_from_id($maintenanceinfo['customer_id']);
        
        if($email_cv_email)
        {
            mailing::email_stuff_plaintxt($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],"\"".$customer_info['cv_name']."\" <".$customer_info['cv_default_email'].">",$email_subject,$email_txt);
        }
        foreach($cv_contact_emails as $cv_contact_email)
        {
            $send_to_email = contact::get_email_from_email_id($cv_contact_email);
            mailing::email_stuff_plaintxt($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],"\"".$send_to_email['firstname']." ".$send_to_email['lastname']."\" <".$send_to_email['email_address'].">",$email_subject,$email_txt);
        }
        casesystem::add_entry("NULL", "EMAIL SENT TO ".$customer_info['cv_name']."&lt;".$customer_info['cv_default_email']."&gt;"."\n------------------------------------\n".$email_txt, $hiddencasesystem_txt, $case_id_to_email, $USER->GetUserID());
        maintenance::setUpdated($maintenance_id);
        
    break;
    case "Save and Close":
        maintenance::update_maintenance($maintenance_id, $maintenance_title, $maintenance_txt, 'YES');
        maintenance::close_maintenance($maintenance_id, $USER->GetUserID());
    break;
    case "Add Vendor to Ticket":
    	try{
        maintenance::addMaintenanceMtmCVMain($maintenance_id, $vendor_id);
    	}
    	catch(CVException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        	
        	
        }        
    break;
    case "Remove MaintenanceCVTag":
    	try{
        maintenance::removeMaintenanceMtmCVMain($maintenance_id, $cv_id);
    	}
    	catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        	
        	
        }        
    break;
    case "Create Case":
        $case_id = casesystem::create_case("NULL", $USER->GetUserID(), $casesystem_main_assigned, $casesystem_txt, $hiddencasesystem_txt, $case_title);
        $maintenanceinfo = maintenance::get_maintenance($maintenance_id);
        $item_info  =  Inventory_Item::get_inventory_item_from_id($maintenanceinfo['inventory_id']);
        try{
            casesystem::tagWithCVID($case_id, $maintenanceinfo['customer_id']);
        }
        catch(CasesystemException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] .= $exception->message;
        }
        try{
            casesystem::tagWithCVID($case_id, $item_info['cv_id']);
        }
        catch(CasesystemException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] .= $exception->message;
            
            
        }
        maintenance::add_case('NULL', $case_id, $maintenance_id);
        
        break;
   
    default:
}
$maintenanceinfo = maintenance::get_maintenance($maintenance_id);
$timestamp = strtotime($maintenanceinfo['whenfollowup']);
$followup_date_year = date('Y', $timestamp);
$followup_date_month = date('m', $timestamp);
$followup_date_day = date('d', $timestamp);
$followup_date_hour = date("h", $timestamp);
$followup_date_minute = date("i", $timestamp);

$item_info  =  Inventory_Item::get_inventory_item_from_id($maintenanceinfo['inventory_id']);
$prop = Property::get_property($item_info['property_id']);
$body = $maintenanceinfo['maintenance_title']."&#13;\n";
$body .= $prop['property_address']."  ".$prop['property_aptnum']."&#13;\n";
$body .= $prop['property_city']."  ".$prop['property_state']."  ".$prop['property_zip']."&#13;\n&#13;\n";
$email_owner_body .= $body;
$email_owner_body .= "Hello ".$item_info['cv_name']." your tenant has notified us of the following issue:&#13;\n&#13;\n";
$email_owner_body .= $maintenanceinfo['maintenance_txt']."&#13;\n";
$body .= $maintenanceinfo['maintenance_txt']."&#13;\n";
$email_owner_body .= "We are sending a technician to estimate/repair the problem, as needed.&#13;\n&#13;\n";
$email_owner_body .= "KM Realty, Inc&#13;\n&#13;\n";

$email_owner_body .= "Hello ".$item_info['cv_name']." we have sent a technician to repair following issue:&#13;\n&#13;\n";
$email_owner_body .= $maintenanceinfo['maintenance_txt']."&#13;\n";
$email_owner_body .= "The charge for this service will be $ and will be deducted out of ";
$email_owner_body .= "the following month's rent check.  Please log on to your account at "; 
$email_owner_body .= "www.kmrealtysd.com to view the invoice.&#13;\n&#13;\n";
$email_owner_body .= "KM Realty, Inc&#13;\n&#13;\n";



$customer_info = CV_Main::get_cv_from_id($maintenanceinfo['customer_id']);
$body .= "\n";
$body .= "Tenant Info \n";
$body .= $customer_info['cv_name']."\n";
if($customer_info['cv_default_phone'] != '')
{
    $body .= $customer_info['cv_default_phone']." \n";
}
if($customer_info['cv_default_email'] != '')
{
    $body .= $customer_info['cv_default_email']." \n";
}
$customer_contacts = CV_Main::getall_cv_contacts_from_id($maintenanceinfo['customer_id']);
if(is_array($customer_contacts) && count($customer_contacts) > 0)
{
    $body .= "Tenant Contacts \n";
    foreach($customer_contacts as $customer_contact)
    {
        $body .= $customer_contact['firstname']." ".$customer_contact['lastname']."\n";;
        $c_phones = contact::getall_phonenumbers_from_contact_id($customer_contact['contacts_id']);
        foreach($c_phones as $c_phone)
        {
            $body .= $c_phone['phonetype_name']." ".$c_phone['phone_num']."\n";;
        }
    }
    $body .= "\n";
}
$email_body = $body; 
$body .= "&#13;\n";
$body .= "Owner Info &#13;\n";
$body .= $item_info['cv_name']."&#13;\n";;
$body .= $item_info['cv_default_phone']." &#13;\n";
$body .= $item_info['cv_default_email']." &#13;\n";
$owners = CV_Main::getall_cv_contacts_from_id($item_info['cv_id']);
foreach($owners as $owner)
{
    $body .= "\n";
    $body .= "Owner Contacts \n";
    $body .= $owner['firstname']." ".$owner['lastname']."\n";
    $o_phones = contact::getall_phonenumbers_from_contact_id($owner['contacts_id']);
    foreach($o_phones as $o_phone)
    {
        $body .= $o_phone['phonetype_name']." ".$o_phone['phone_num']."\n";;
    }
}
$casesystem_txt = $body;                       
$case_title = $prop['property_address']."  ".$prop['property_aptnum']."- ".$maintenanceinfo['maintenance_title'];
	        
switch($ACTION)
{
    case "Open Maintenance Issue":
        $case_id = casesystem::create_case("NULL", $USER->GetUserID(), $USER->GetUserID(), $casesystem_txt, $hiddencasesystem_txt, $case_title, "","");
        //add the tenant, then the owner to the case
        try{
            casesystem::tagWithCVID($case_id, $maintenanceinfo['customer_id']);
        }
        catch(CasesystemException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] .= $exception->message;
        }
        try{
            casesystem::tagWithCVID($case_id, $item_info['cv_id']);
        }
        catch(CasesystemException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] .= $exception->message;
            
            
        }
        
        maintenance::add_case('NULL', $case_id, $maintenance_id);
        foreach($customer_contacts as $customer_contact)
        {
            maintenance::add_maintenance_mtm_contacts($maintenance_id, $customer_contact['contacts_id']);
        }
        foreach($owners as $owner)
        {
            maintenance::add_maintenance_mtm_contacts($maintenance_id, $owner['contacts_id']);
        }   
    break;
}

$users = Rbac_User::getAllAllowedTo("access_module", "maintenance_module");
foreach($users as $user)
{
    $userArray[$user['user_id']] = $user['username'];
}
$userArray[0] = "OPEN";

$cases = maintenance::get_maintenance_cases($maintenance_id);
//print_r($cases);
//$maintenance_contacts = maintenance::get_contacts_of_maintenance($maintenance_id);
$maintenance_mtm_vendors = maintenance::getVendorsOnTicket($maintenance_id);
$maintenance_vendors = CV_Main::getall_vendors_with_tag('Maintenance');
$common_vendors = CV_Main::getall_vendors_with_tag('Frequently Used');
$_SERVER['QUERY_STRING'] = "maintenance_id=".$maintenance_id; 


include("maintenance_update.phtml");