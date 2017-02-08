<?
include("../../../framework/framework_masterinclude.php");
include("contacts_include.php");
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
        contacts::add_activity("NULL", $activity_title, $activity_txt, $USER->GetUserID(), $contacts_id);
        header('Content-Type: text/xml');
        echo "<transaction>SAVED</transaction>";
        break;
    default:
}
            


?>
