<?php
/*
*
*/
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../../openMobas/framework_masterinclude.php");
include("admin_include.php");

$sortby = "name";
$sortby2 = "description";
$ACTION = '';
$id = "NULL";
$ACTION_VALUE = "Add!";
$edit_id = "";
$name = '';
$description = ''; 

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$RBAC = new Rbac($dbh, $USER, $rbac_user, $objSession);

if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}
if(isset($_POST['ACTION']))
{
    @$id =  $_POST['id'];
    @$name =  $_POST['name'];
    @$description =   $_POST['description'];
    @$privilege_actions =    $_POST['privilege_actions'];
    @$edit_id =         $_POST['edit_id'];
        
    $ACTION = $_POST['ACTION'];
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
        $privilege_id = $RBAC->create_privilege($id, $name, $description, 0);
        foreach($privilege_actions as $privilege_action)
        {
            $RBAC->create_privilege_mtm_action("NULL", $privilege_id, $privilege_action);
        }
        break;
    case "View / Edit":
        $ACTION_VALUE = "Save!";
        $privilege = $RBAC->get_privilege_by_ID($edit_id);
        $id = $privilege['privilege_id'];
        $name = $privilege['name'];
        $description = $privilege['description'];
        $pmtmas = $RBAC->get_all_pmtma_by_privilege_iD($edit_id);
        $string = '';
        foreach($pmtmas as $pmtma)
        {
            $string .= $pmtma['action_id'].", ";
        }
        $string = substr($string, 0, -2);
        if(strlen($string) > 0)
        {
            unset($actions);
            $actions = $RBAC->get_all_unused_actions($string);
        }
        break;
    case "Save!":
        $RBAC->update_privilege($id, $name, $description);
        if(isset($privilege_actions) && is_array($privilege_actions))
        {
        foreach($privilege_actions as $privilege_action)
        {
            $RBAC->create_privilege_mtm_action("NULL", $id, $privilege_action);
        }
        }
        $id = "NULL";
        $name = "";
        $description = "";
        break;
    case "Delete":
        $RBAC->delete_privilege($edit_id);
        break;
}

$privileges = $RBAC->get_All_privileges($sortby, $sortby2);
$priv_count = $RBAC->count_privileges();

include("admin_rbac_privileges.phtml");

