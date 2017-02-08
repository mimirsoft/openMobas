<?php
/*

*/
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../../openMobas/framework_masterinclude.php");
include("admin_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$sortby = "username";
$sortby2 = "description";
$ACTION = '';
$id = "NULL";
$ACTION_VALUE = "Add!";
$RBAC = new Rbac($dbh, $USER, $rbac_user, $objSession);
// set this to empty for when not editing
$user_id = '';
$role_id = '';


if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$id = $_POST['id'];
    @$user = $_POST['user'];
    @$role = $_POST['role'];
    @$edit_id = $_POST['edit_id'];
    
}    
if(isset($_GET['sortby']))
{
    $sortby = $_GET['sortby'];
    $sortby2 = $_GET['sortby2'];
}

$actions = $RBAC->get_all_actions("name");
switch ($ACTION)
{
    case "Add!":
        $privilege_id = $RBAC->create_users_mtm_roles($id, $user, $role);
        break;
    case "View / Edit":
        $ACTION_VALUE = "Save!";
        $umtmr = $RBAC->get_users_mtm_roles_by_ID($edit_id);
        $id = $umtmr['id'];
        $role_id = $umtmr['role_id'];
        $user_id = $umtmr['user_id'];
        break;
    case "Save!":
        $RBAC->update_users_mtm_roles($id, $user, $role);
        $id = "NULL";
        break;
    case "Delete":
        $RBAC->delete_users_mtm_roles_by_ID($edit_id);
        break;
}

$users = $USER->get_users($sortby);
$roles = $RBAC->get_all_roles($sortby2);
$user_mtm_role = $RBAC->get_all_users_mtm_roles($sortby2, $sortby2);
include("admin_rbac_users_has_roles.phtml");

