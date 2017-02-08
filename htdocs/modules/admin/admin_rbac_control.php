<?php
/*
* This file is simple example on how to use the RBAC system
* You must first run the setup.php file before running this file.
*/

require_once("../../../../openMobas/framework_masterinclude.php");
include("admin_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

include("admin_rbac_control.phtml");



?>