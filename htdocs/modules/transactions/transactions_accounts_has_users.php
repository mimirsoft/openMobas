<?php
/*
* This file is simple example on how to use the RBAC system
* You must first run the setup.php file before running this file.
*/
$sortby = "username";
$sortby2 = "description";
$ACTION = '';
$id = "NULL";
$ACTION_VALUE = "Add!";
$edit_id = "";
$user_id = "";
$account = "";

include("../../../framework/framework_masterinclude.php");
include("transactions_include.php");
include("../admin/admin_functions.php");
Framework::authenticate('Unlimited');//the two includes must be before the authenticate, to supply the needed module name for authentication
if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "transactions_module")))
{
    echo "PERMISSION DENIED TO ACCESS MODULE TRANSACTIONS!!";
    exit;
}

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_GET->{$key});
}
switch ($ACTION)
{
    case "Add!":
        $umtma = transactions::create_users_mtm_accounts($id, $user, $account);
        break;
    case "View / Edit":
        $ACTION_VALUE = "Save!";
        $umtmr = transactions::get_users_mtm_accounts_by_id($edit_id);
        $id = $umtmr['id'];
        $account_id = $umtmr['account_id'];
        $user_id = $umtmr['user_id'];
        break;
    case "Save!":
        transactions::update_users_mtm_accounts($id, $user, $account);
        $id = "NULL";
        break;
    case "Delete":
        transactions::delete_users_mtm_accounts($edit_id);
        break;
}

$users = admin::get_users($sortby);
$account_array = transactions::build_accountIDtoFullname_array(true);
$account_stack = transactions::build_account_stack_all();
$user_mtm_account = transactions::get_all_users_mtm_accounts($sortby2, $sortby2);
include("transactions_accounts_has_users.phtml");

