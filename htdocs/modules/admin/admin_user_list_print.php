<?
require_once("../../../framework/framework_masterinclude.php");
require_once("admin_include.php");
include('../cv_master/Inventory_Item.class.php');

Framework::authenticate();
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}

//We initialize all our variables.
$users = '';

if(isset($_GET['ACTION']))
{
     $ACTION = $_GET['ACTION'];
     $user_search = $_GET['user_search'];
}
switch($ACTION)
{
    case "Print":
        $users = User::getall_users_interested_in_item($user_search['interest']);
        include("admin_user_list_print.phtml");
        
    break;
    case "Print Email":
        $users = User::getall_users_interested_in_item($user_search['interest']);
        include("admin_user_list_print_email.phtml");
    break;
        

}



?>