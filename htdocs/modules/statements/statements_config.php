<?
include("../../../framework/framework_masterinclude.php");
include("statements_include.php");
framework::authenticate("Unlimited");//the two includes must be before the authentica, to supply the needed module name for authentication

$statement[] = '';
$statement_id = 'NULL';
$ACTION = '';
$edit_statement = '';
$statement['element_count'] = 0;
$statement['body']['arows'] = 0;
$statement['body']['drows'] = 0;

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
switch($ACTION)
{
    case "EDIT":
        $statement_object = statements::get_statement_by_id($edit_statement);
        $statement = unserialize($statement_object['statement_array']);
        $statement_id = $statement_object['statement_id'];
        $statement_privilege = $statement_object['statement_privilege'];
        break;
    case "SAVE":
        $statement['element_count']= count($statement['body']);
        $privilege = $statement['privilege'];
        $statement_new_id = statements::save_statement($statement_id, $statement['name'], $statement, $privilege);
        if($statement_new_id == 0)
        {
            $statement_object = statements::get_statement_by_id($statement_id);
        }
        else
        {
            $statement_object = statements::get_statement_by_id($statement_new_id);
        }
        $statement = unserialize($statement_object['statement_array']);
        $statement_id = $statement_object['statement_id'];
        $statement_privilege = $statement_object['statement_privilege'];
        break;
    case "DELETE":
        statements::delete_statement($edit_statement);
        break;
}        
$dbh = new DB_Mysql();
$accountIDtoFullnameArray = transactions::getall_account_info("N");
$append['account_id'] = "user_defined";
$append['account_fullname'] = "DEFINED AT RUNTIME";
array_unshift($accountIDtoFullnameArray, $append);
$stmt = $dbh->prepare("SELECT statement_id, statement_name FROM statements_main ORDER BY statement_name  ");
$stmt->execute();	
$statement_list = $stmt->fetchall_assoc();
include("statements_config.phtml");


?>

