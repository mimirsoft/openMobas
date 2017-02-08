<?
include("../../../framework/framework_masterinclude.php");
require_once("maintenance_include.php");
require_once("../../../framework/classes/Casesystem.class.php");
require_once("../../../framework/classes/Maintenance.class.php");

Framework::authenticate('Limited');//the two includes must be before the authentica, to supply the needed module name for authentication
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if(isset($_POST['maintenance_tickets']))
{
	@$maintenance_tickets = $_POST['maintenance_tickets'];
    
}
$users = Rbac_User::getAllAllowedTo("access_module", "maintenance_module");
foreach($users as $user)
{
    $userArray[$user['user_id']] = $user['username'];
}

foreach($maintenance_tickets as $maintenance_id)
{
    $maintenanceinfo = maintenance::get_maintenance($maintenance_id);
    $cases = maintenance::get_maintenance_cases($maintenance_id);
    if(is_array($cases))
    {
        foreach($cases as $row)
        {
            $case_id = $row['case_id'];
            $caseinfo = casesystem::get_case($case_id);
            $dbh = new DB_Mysql();
            $stmt = $dbh->prepare("SELECT * FROM casesystem_entry WHERE case_id=:1: ");
            $stmt->execute($case_id);
            $caseentrys = $stmt->fetchall_assoc();
            
        }
    }

    include("maintenance_print.phtml");
    unset($cases);
    unset($caseinfo);
    
}   


