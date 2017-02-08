<?
include("../../../framework/framework_masterinclude.php");
include("yearend_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

$system_id = 1;
$statement_object = Framework::get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);
foreach ($_POST as $key => $value)
{
    $$key = $value;
}

include("1099.css");

include("yearend_1099manual.phtml");


?>
