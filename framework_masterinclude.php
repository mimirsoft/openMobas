<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("globalconstants.php");
require_once("classes/Database_Mysql.class.php");
require_once("classes/Framework.class.php");
require_once("classes/User_Session.class.php");
require_once("classes/User.class.php");
require_once("classes/Rbac_User.class.php");

$dbh = new DB_Mysql($DB_SETTINGS);
$objSession = new User_Session($dbh);
$objSession->Impress();
$USER = $objSession->GetUserObject();
$rbac_user = new Rbac_User($dbh, $USER);

$FRAMEWORK = new Framework($dbh, $USER, $rbac_user);
$FRAMEWORK->setModules($INSTALLED_MODULES);


?>
