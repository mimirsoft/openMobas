<?php
include("../../../framework/framework_masterinclude.php");
Framework::XML_authenticate();
header('Content-Type: text/xml');
echo "<login>TRUE</login>";
exit;
?>
