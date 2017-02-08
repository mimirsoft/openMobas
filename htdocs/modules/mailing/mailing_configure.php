<?php
include("../../../framework/framework_masterinclude.php");
include("mailing_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$WARNING['show'] = false;

$ACTION = '';
$mailing_id = ''; 
$mailing_name = '';
$mailing_body = ''; 
foreach ($_POST as $key => $value)
{
    $$key = $value;
}

if ($ACTION == "Save Mailing")
{
    mailing::add_mailing($mailing_id, $mailing_name, $mailing_body);
    $mailing_id = "NULL";
}

if ($ACTION == "Edit Mailing")
{
    $dbRow = mailing::get_mailing_from_id($mailing_edit);
    $mailing_id = $dbRow['mailing_id'];
    $mailing_name = $dbRow['mailing_name'];
    $mailing_body = $dbRow['mailing_body'];
}

if ($mailing_id == "")
{
    $mailing_id = "NULL";
}

$mailings = mailing::getall_mailings();

                
include("mailing_configure.phtml");

?>
