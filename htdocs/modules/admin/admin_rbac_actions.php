<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
* This file is simple example on how to use the RBAC system
* You must first run the setup.php file before running this file.
*/

require_once("../../../../openMobas/framework_masterinclude.php");
include("admin_include.php");

$sortby = "name";
$ACTION = '';
$id = "NULL";
$ACTION_VALUE = "Add!";
$edit_id = "";
$name = '';
$description = ''; 

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}
$RBAC = new Rbac($dbh, $USER, $rbac_user, $objSession);

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_GET->{$key});
}
switch ($ACTION)
{
    case "Add!":
        try{
            $RBAC->create_action($id, $name, $description);
        }
        catch(RBACException $e)
        {
            echo $e->message;
        }
        break;
    case "View / Edit":
        $ACTION_VALUE = "Save!";
        $action = $RBAC->get_action_by_ID($edit_id);
        $id = $action['action_id'];
        $name = $action['name'];
        $description = $action['description'];
        break;
    case "Save!":
        $RBAC->update_action($id, $name, $description);
        $id = "NULL";
        $name = "";
        $description = "";
        break;
    case "Delete":
        $RBAC->delete_action($edit_id);
        $id = "NULL";
        $name = "";
        $description = "";
        break;
}

$actions = $RBAC->get_all_actions($sortby);
$act_count = $RBAC->count_actions();

include("admin_rbac_actions.phtml");

