<?
include("../../../framework/framework_masterinclude.php");
include("workorder_include.php");
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
        workorder::add_entry("NULL", $woentry_txt, $wo_id, $USER->GetUserID());
        workorder::update_case($wo_id, $USER->GetUserID(), $whoassigned_id, $wo_title);
        header('Content-Type: text/xml');
        echo "<transaction>SAVED</transaction>";
        break;
    case "close_case":
        if($woentry_txt != "")
        {
            workorder::add_entry("NULL", $woentry_txt, $wo_id, $USER->GetUserID());
        }
        workorder::add_entry("NULL", "CLOSED BY ".$USER->GetUserName(), $wo_id, $USER->GetUserID());
        workorder::close_case($wo_id, $USER->GetUserID(), $whoassigned_id, $wo_title);
        header('Content-Type: text/xml');
        echo "<transaction>SAVED</transaction>";
        break;
    
    default:
}
            


?>
