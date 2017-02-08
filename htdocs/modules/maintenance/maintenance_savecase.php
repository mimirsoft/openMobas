<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("maintenance_include.php");
require_once("../../../framework/classes/Maintenance.class.php");
require_once("../../../framework/classes/Casesystem.class.php");
Framework::XML_authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "maintenance_module"))
{
    echo "Permission access Maintenance Module";
    exit;
}

$ACTION = '';
//this is uncommented to test this in a browser
//$_POST = $_GET; 

if(isset($_POST['ACTION']))
{
	$ACTION = $_POST['ACTION'];
    @$maintenance_id = $_POST['maintenance_id'];
	@$casesystem_txt = $_POST['casesystem_txt'];
    @$hiddencasesystem_txt = $_POST['hiddencasesystem_txt'];
    @$case_id = $_POST['case_id'];
    @$case_title = $_POST['case_title'];
    @$action_needed = $_POST['action_needed'];
    @$status_text = $_POST['status_text'];
	@$whoassigned_id = $_POST['whoassigned_id'];
    @$followup = $_POST['followup'];
     
}

switch($ACTION)
{
    case "add_update":
        if($casesystem_txt != "")
        {
            casesystem::add_entry("NULL", $casesystem_txt, $hiddencasesystem_txt, $case_id, $USER->GetUserID());
        }
        casesystem::update_case($case_id, $USER->GetUserID(), $whoassigned_id, $case_title, $action_needed, $status_text);
        maintenance::setUpdated($maintenance_id);
        
        header('Content-Type: text/xml');
        echo "<transaction>SAVED</transaction>";
        break;
    case "set_followup":
        casesystem::add_entry("NULL", "Follow Up Set:".$followup, "", $case_id, $USER->GetUserID());
        casesystem::setFollowUp($case_id, $USER->GetUserID(), $followup);
        maintenance::setUpdated($maintenance_id);
        
        header('Content-Type: text/xml');
        echo "<transaction>SAVED</transaction>";
        break;
    case "close_case":
        if($casesystem_txt != "")
        {
            casesystem::add_entry("NULL", $casesystem_txt, $hiddencasesystem_txt, $case_id, $USER->GetUserID());
        }
        casesystem::add_entry("NULL", "CASE CLOSED BY ".$USER->GetUserName(), $hiddencasesystem_txt, $case_id, $USER->GetUserID());
        casesystem::close_case($case_id, $USER->GetUserID(), $whoassigned_id, $case_title);
        maintenance::setUpdated($maintenance_id);
        header('Content-Type: text/xml');
        echo "<transaction>SAVED</transaction>";
        break;
    
    default:
}
            


?>
