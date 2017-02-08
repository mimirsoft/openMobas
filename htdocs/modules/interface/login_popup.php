<?php

include("../../../framework/framework_masterinclude.php");
$MODULE_NAME = "module_none"; 
/*this must be set to pass the module permission checker,
 for the default main page, which is not part of any module
*/
Framework::authenticate();

?>

<script language="JavaScript"

type="text/javascript">
window.addEventListener("load", window.close(), true)

</script>