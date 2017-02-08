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

$ACTION = "";
if(isset($_POST['ACTION']))
{
    $calltype = $_POST['calltype'];
    $ACTION = $_POST['ACTION'];
}

switch($ACTION)
{
    case "OPEN":
        switch($calltype)
        {
            case "inquiry":
                $items = inventory_manager::getall_available_items();
                include("calllog_inquiry.phtml");
            break;
        }
    break;
    case "Search":
    break;
    default:
        include("calllog_main.phtml");
     break;
}

?>
