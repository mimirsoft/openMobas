<?
include("../../../framework/framework_masterinclude.php");
include("yearend_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

include("yearend_manual.phtml");



?>
