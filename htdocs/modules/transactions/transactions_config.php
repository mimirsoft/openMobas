<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("transactions_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "transactions_module")))
{
    echo "PERMISSION DENIED TO ACCESS MODULE TRANSACTIONS!!";
    exit;
}
$action = '';
$account_id = '';
$account_name = '';
$account_parent = '';
$accounttype_id = '';
$account_memo = '';
$account_starting = '';
$account_current = '';
$defaultstatement_type = '';
$page = 0;
$results = array();
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
switch($action)
{
    case "Save Account":
        if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "create_account", "transactions_module")))
        {
            echo "PERMISSION DENIED TO CREATE ACCOUNT IN TRANSACTIONS MODULE!!";
            exit;
        }
        if ($account_name != "")
        {
            transaction::add_accountnew($account_id, $account_name, $account_parent, $accounttype_id, $account_memo, $account_current);
            $account_id = "";
            $account_name = "";
            $accounttype_id = "";
            $account_parent = "";
            $account_memo = "";
        }
        break;
    case "Verify Names":
        transaction::verify_names();
        break;
    case "Search":
        $results = transaction::search_accounts($search_string);
        break;
    case "Verify Tree":
        transaction::verify_tree();
        break;
}

$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT * FROM transactions_accounttype ORDER BY accounttype_name  ");
$stmt->execute();	
$accounttypes_num = $stmt->num_rows();
$accounttypes = $stmt->fetchall_assoc();
$stmt = $dbh->prepare("SELECT COUNT(*) FROM transactions_accounts ");
$stmt->execute();	
$count = $stmt->fetch_assoc();
$account_num = $count['COUNT(*)'];
$page_count = floor($account_num/200);
$account_array = transaction::build_accountIDtoFullname_array(true);
$starting_point = $page*200;
$account_stack = transaction::build_account_stack($starting_point, 200);
$account_stack_all = transaction::build_account_stack_all();
include("transactions_config.phtml");


?>






