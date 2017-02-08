<?
include("../../../framework/framework_masterinclude.php");
include("contacts_include.php");
Framework::authenticate('Limited');//the two includes must be before the authentica, to supply the needed module name for authentication

$print = false;
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}

$dbh = new DB_Mysql();


$userArray = framework::getUserArray();

$stmt = $dbh->prepare("SELECT * FROM contacts_activity WHERE contacts_id=:1: ");
$stmt->execute($contacts_id);
$caseentrys = $stmt->fetchall_assoc();
if($print)
{
    include("casesystem_print.phtml");
}
else{
    include("contacts_activityupdate.phtml");
}