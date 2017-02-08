<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once("../../../framework/framework_masterinclude.php");
require_once("tenants_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "tenants_module"))
{
    echo "Permission access Customer/Vendor Module";
    exit;
}
$tenant_status = 'Y';

$tenants = Tenant::getAllTenantsByStatus($tenant_status, "thirty_day_date", "cv_name");

include("tenants_export.phtml");

?>
