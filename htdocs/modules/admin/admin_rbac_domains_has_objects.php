<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
/*
* This file is simple example on how to use the RBAC system
* You must first run the setup.php file before running this file.
*/
require_once("../../../../openMobas/framework_masterinclude.php");
include("admin_include.php");

$sortby = "is_singular";
$sortby2 = "domain_name";
$sortby3 = "object_name";
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
    case "Delete":
        foreach($edit_ids as $edit_id)
        {
            $RBAC->delete_dmtmo($edit_id);
        }
        $id = "NULL";
        $name = "";
        $description = "";
        break;
}
$dmtmo_count = $RBAC->count_dmtmo();

$dmtmo = $RBAC->get_all_domains_mtm_objects($sortby, $sortby2, $sortby3);

include("admin_rbac_domains_has_objects.phtml");

