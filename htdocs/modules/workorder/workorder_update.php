<?
require_once("../../../framework/framework_masterinclude.php");
require_once("workorder_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

$print = false;
if(isset($_GET['wo_id']))
{
    $wo_id = $_GET['wo_id'];
}

$users = Rbac_User::getAllAllowedTo("access_module", "maintenance_module");
foreach($users as $user)
{
    $userArray[$user['user_id']] = $user['username'];
}
$userArray[0] = "OPEN";

$woinfo = workorder::get_workorder($wo_id);
$woentrys = workorder::get_workorder_entries($wo_id);
print_r($caseinfo);
if($print)
{
    include("workorder_print.phtml");
}
else{
    include("workorder_update.phtml");
}