<?
include("../../../framework/framework_masterinclude.php");
include("casesystem_include.php");
include("../../../framework/classes/Casesystem.class.php");
Framework::XML_authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
switch($ACTION)
{
    case "add_update":
        if($casesystem_txt != "")
        {
            casesystem::add_entry("NULL", $casesystem_txt, $hiddencasesystem_txt, $case_id, $USER->GetUserID());
        }
        casesystem::update_case($case_id, $USER->GetUserID(), $whoassigned_id, $case_title, $action_needed, $status_text);
        header('Content-Type: text/xml');
        echo "<transaction>SAVED</transaction>";
        break;
    case "set_followup":
        casesystem::add_entry("NULL", "Follow Up Set:".$followup, "", $case_id, $USER->GetUserID());
        casesystem::setFollowUp($case_id, $USER->GetUserID(), $followup);
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
        header('Content-Type: text/xml');
        echo "<transaction>SAVED</transaction>";
        break;
    
    default:
}
            


?>
