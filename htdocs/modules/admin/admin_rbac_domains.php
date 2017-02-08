<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
* This file is simple example on how to use the RBAC system
* You must first run the setup.php file before running this file.
*/

include("../../../framework_masterinclude.php");
include("admin_include.php");

$sortby = "name";
$sortby2 = "name";
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
$objects = $RBAC->get_all_objects("name");
switch ($ACTION)
{
    case "Add!":
        $domain_id = $RBAC->create_domain($id, $name, $description, 0);
        foreach($domain_objects as $domain_object)
        {
            $RBAC->create_domain_mtm_object("NULL", $domain_id, $domain_object);
        }
        break;
    case "View / Edit":
        $ACTION_VALUE = "Save!";
        $domain = $RBAC->get_domain_by_ID($edit_id);
        $id = $domain['domain_id'];
        $name = $domain['name'];
        $description = $domain['description'];
        $dmtmos = $RBAC->get_all_dmtmo_by_domain_iD($edit_id);
        $string = '';
        foreach($dmtmos as $dmtmo)
        {
            $string .= $dmtmo['object_id'].", ";
        }
        $string = substr($string, 0, -2);
        if(strlen($string) > 0)
        {
             unset($objects);
            $objects = $RBAC->get_all_unused_objects($string);
        }
        break;
    case "Save!":
        $RBAC->update_Domain($id, $name, $description);
        foreach($domain_objects as $domain_object)
        {
            $RBAC->create_domain_mtm_object("NULL", $id, $domain_object);
        }
        $id = "NULL";
        $name = "";
        $description = "";
        break;
    case "Delete":
        $RBAC->delete_domain($edit_id);
        break;
}

$domains = $RBAC->get_All_Domains($sortby, $sortby2);
$dom_count = $RBAC->count_Domains();

include("admin_rbac_domains.phtml");

