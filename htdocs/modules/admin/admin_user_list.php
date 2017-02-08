<?php


require_once("../../../../openMobas/framework_masterinclude.php");
require_once("admin_include.php");
require_once("../../../../openMobas/classes/Inventory_Item.class.php");


$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}

//We initialize all our variables.
$users = '';
$ACTION = '';

if(isset($_POST['ACTION']))
{
     $ACTION = $_POST['ACTION'];
     $user_search = $_POST['user_search'];
}
switch($ACTION)
{
    case "Search":
        $users = User::getall_users_interested_in_item($user_search['interest']);
    break;


}

$unconfirmed_users = User::get_temp_users();

$available = Inventory_Item::getall_available_items();

include("admin_user_list.phtml");

?>