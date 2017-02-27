<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
include("admin_include.php");
/*
*
*
*/
$sortby = "is_singular";
$sortby2 = "name";
$ACTION = '';
$id = "NULL";
$ACTION_VALUE = "Add!";
$edit_id = "";
$name = '';
$description = ''; 
$is_allowed = 0;
$role_id = '';
$domain_id = '';
$privilege_id = '';
$RBAC = new Rbac($dbh, $USER, $rbac_user, $objSession);



$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);//the two includes must be before the authenticate, to supply the needed module name for authentication

if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}
if(isset($_POST['ACTION']))
{
    @$id =  $_POST['id'];
    @$privilege =  $_POST['privilege'];
    @$role =   $_POST['role'];
    @$domain =    $_POST['domain'];
    @$is_allowed =         $_POST['is_allowed'];
    @$edit_id =         $_POST['edit_id'];

    $ACTION = $_POST['ACTION'];
}
if(isset($_GET['SORTBY']))
{
    $SORTBY = $_GET['SORTBY'];
}

$actions = $RBAC->get_all_actions("name");
switch ($ACTION)
{
    case "Add!":
        $privilege_id = $RBAC->create_permission($id, $role, $privilege, $domain, $is_allowed);
        break;
    case "View / Edit":
        $ACTION_VALUE = "Save!";
        $privilege = $RBAC->get_permission_by_ID($edit_id);
        $id = $privilege['id'];
        $is_allowed = $privilege['is_allowed'];
        $role_id = $privilege['role_id'];
        $privilege_id = $privilege['privilege_id'];
        $domain_id = $privilege['domain_id'];
        break;
    case "Save!":
        $RBAC->update_permission($id, $role, $privilege, $domain, $is_allowed);
        $id = "NULL";
        $name = "";
        $description = "";
        $is_allowed = 1;
        break;
    case "Delete":
        $RBAC->delete_permission($edit_id);
        break;
}

$permissions = $RBAC->get_All_permissions($sortby, $sortby2);
$roles = $RBAC->get_All_roles($sortby2);
$privileges = $RBAC->get_All_privileges($sortby, $sortby2);
$domains = $RBAC->get_All_domains($sortby, $sortby2);

include("admin_rbac_roles_has_permissions.phtml");

