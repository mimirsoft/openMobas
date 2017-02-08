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

$stmt = $dbh->prepare("SELECT * FROM contacts_activity INNER JOIN contacts_main ON contacts_main.contacts_id = contacts_activity.contacts_id ORDER by contact_activity_date LIMIT 50");
$stmt->execute();
$caseentrys = $stmt->fetchall_assoc();
if($print)
{
    include("casesystem_print.phtml");
}
else{
    include("contacts_activityview.phtml");
}