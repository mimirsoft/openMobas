<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../framework/framework_masterinclude.php");
require_once("mailing_include.php");
require_once("../../../framework/classes/Mailing.class.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$WARNING['show'] = false;

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "mailing_module"))
{
    echo "Permission Denied to access check module.";
    exit;
}

$system_id = 1;
$statement_object = Framework::get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);


$date_year = date('Y');
$date_month = date('m');
$date_day = date('d');

foreach ($_POST as $key => $value)
{
	$$key = $value;
	unset($_POST->{$key});
}

$date = $date_year."-".$date_month."-".$date_day;

$mailings = mailing::getall_mailings();

include("mailing_main.phtml");



?>

