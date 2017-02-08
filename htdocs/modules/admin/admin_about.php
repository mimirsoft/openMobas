<?php

require_once("../../../framework/framework_masterinclude.php");
require_once("admin_include.php");

Framework::authenticate();

include($MODULE_NAME."_rbac.php");

include($MODULE_NAME."_about.phtml");

?>