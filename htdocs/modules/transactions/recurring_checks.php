<?
include("../../../framework/framework_masterinclude.php");
include("recurring_include.php");
Framework::authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication


foreach ($_POST as $key => $value)
{
	$$key = $value;
	unset($_POST->{$key});
}
$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT * FROM recurring_type ORDER BY recurringtype_name");
$stmt->execute();
while($row =  $stmt->fetch_assoc())
{
   $types[$row['recurringtype_id']] = $row['recurringtype_name'];
}

$stmt = $dbh->prepare("SELECT * FROM recurring_main");
$stmt->execute();
$recurring = $stmt->fetchall_assoc();
$accountIDtoFullnameArray = transactions::build_accountIDtoFullName_array(false);
include("recurring_checks.phtml");




?>
