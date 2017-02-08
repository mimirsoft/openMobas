<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../../openMobas/framework_masterinclude.php");
include("admin_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}
//We initialize all our variables.
$system_id = 'NULL';
$SYS_INFO = '';
$sys_info_str = '';
$ACTION = '';
if(isset($_POST['ACTION']))
{
    @$system_id =  $_POST['system_id'];
    @$system_name =  $_POST['system_name'];
    @$SYS_INFO =  $_POST['SYS_INFO'];
    @$sys_info_str =   $_POST['sys_info_str'];
    $ACTION = $_POST['ACTION'];
}

$admin_system = new Admin_System($dbh, $USER, $rbac_user);

if ($ACTION == "Update Info" )
{
    $admin_system->update_system($system_id, $system_name, $SYS_INFO);
}
if($ACTION == "EDIT SYSTEM")
{
    $statement_object = $FRAMEWORK->get_system($system_id);
    $SYS_INFO = unserialize($statement_object['system_array']);
    $system_name = $statement_object['system_name'];
}


$systems = $admin_system->get_systems();
include("admin_system.phtml");


?>