<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../../openMobas/framework_masterinclude.php");

$MODULE_NAME = "module_none"; 
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

/*this must be set to pass the module permission checker,
 for the default main page, which is not part of any module
*/
include ("main.phtml");

?>
