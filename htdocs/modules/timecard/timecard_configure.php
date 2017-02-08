<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("timecard_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION = '';
$WARNING['show'] = false;

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $ip = $_POST['ip'];
}
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "configure_module", "timecard_module"))
{
    echo "Permission Denied to configure timecard module.";
    exit;
}
$Timecard_IP = new Timecard_IP();

switch($ACTION)
{
    case "Add IP":
    	try{
    	$Timecard_IP->addIP($ip);
    	}
    	catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        	
        	
        }        
    	break;
    case "Delete IP":
    	$Timecard_IP->deleteIP($ip);        
    	break;
}
//check if clocked in

$IPs = $Timecard_IP->getAllIPs();
include("timecard_configure.phtml");