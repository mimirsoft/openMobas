<?
include("../../../framework/framework_masterinclude.php");
include("workorder_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

$SORTBY =   'whenupdated_date';
$ACTION =     '';
$VIEW_ALL = '';
$VIEW_CLOSED = '';
$wo_title = '';
$woentry_txt = '';
if(isset($_GET['cv_id']))
{
    $cv_id = $_GET['cv_id'];
}
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $whoassigned_id = $_POST['whoassigned_id'];
    $woentry_txt = $_POST['woentry_txt'];
    $wo_title = $_POST['wo_title'];
}

if ($ACTION == "Create")
{
    if($wo_title != '')
    {
        workorder::create("NULL", $USER->GetUserID(), $whoassigned_id, $woentry_txt, $wo_title);
    }
}
$users = Rbac_User::getAllAllowedTo("access_module", "workorder_module");
$userArray = framework::getUserArray();		

$work_orders = workorder::getall($VIEW_CLOSED, $VIEW_ALL, $SORTBY, $USER);

include("workorder_main.phtml");