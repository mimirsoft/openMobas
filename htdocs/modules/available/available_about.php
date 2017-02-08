<?php

require_once("../../../framework/framework_masterinclude.php");
require_once("available_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$WARNING['show'] = false;

include($MODULE_NAME."_rbac.php");

include($MODULE_NAME."_about.phtml");

?>