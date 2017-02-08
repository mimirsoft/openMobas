<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once("../../../framework/framework_masterinclude.php");
require_once("properties_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "properties_module"))
{
    echo "Permission access Customer/Vendor Module";
    exit;
}


$current_props = Property::getall_properties_by_status('CURRENT');

include("properties_export.phtml");

?>
