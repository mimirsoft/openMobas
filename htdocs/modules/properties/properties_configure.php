<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("properties_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$WARNING['show'] = false;

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "properties_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "configure_module", "properties_module"))
{
    echo "Permission Denied to configure admin module.";
    exit;
}

$ACTION = 'NULL';
$propertytype_name = '';
$propertytype_id = 'NULL';
foreach ($_POST as $key => $value)
{
	$$key = $value;
	unset($_POST->{$key});
}
$dbh = new DB_Mysql();

if ($ACTION == "Save Property Type")
{
	property::add_property_type($propertytype_id, $propertytype_name);
	$propertytype_id = "";
	$propertytype_name = "";
}
if ($ACTION == "Edit Property Type")
{
	$stmt = $dbh->prepare("SELECT * FROM properties_type WHERE propertytype_id='$properties_type_edit'");
        $stmt->execute();
	$dbRow = $stmt->fetch_assoc();
	$propertytype_name = $dbRow['propertytype_name'];
	$propertytype_id = $dbRow['propertytype_id'];
}
/*if ($ACTION == "Save Defaults")
{
    properties::delete_setting();
    foreach($MODULE_SETTINGS as $default_name => $default_value)
    {
        properties::save_setting($default_name, $default_value);
    }
    unset($MODULE_SETTINGS);
}
*/

$stmt = $dbh->prepare("SELECT * FROM properties_type ORDER BY propertytype_name  ");
$stmt->execute();
$propertyTypes = $stmt->fetchall_assoc();

$settings = property::getall_settings();
foreach($settings as $row)
{
    $MODULE_SETTINGS[$row['setting_name']] = $row['setting_value'];
}
include("properties_configure.phtml");


?>