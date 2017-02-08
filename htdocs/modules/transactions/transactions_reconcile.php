<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("transactions_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "reconcile_accounts", "transactions_module")))
{
    echo "PERMISSION DENIED TO RECONCILE ACCOUNTS IN TRANSACTIONS MODULE!!";
    exit;
}

$ACTION     =       '';     
$account_id =       '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}

$accountIDtoFullnameArray = transactions::build_accountIDtoFullName_array(false);

$date_year = date('Y');
$date_month = date('m');
$date_day = date('d');

include("transactions_reconcile.phtml");


?>

