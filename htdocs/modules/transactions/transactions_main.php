<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../../openMobas/framework_masterinclude.php");
require_once("transactions_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$WARNING['show'] = false;


if(!($rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "transactions_module")))
{
    echo "PERMISSION DENIED TO ACCESS MODULE TRANSACTIONS!!";
    exit;
}

$transaction = new Transaction($dbh);
$viewall = '';
@$EDIT = $_POST['EDIT'];
if(@$_GET['VIEWALL'] == "Y")
{
    $viewall='Y';
}	
if($EDIT == 'REBALANCE')
{   
    $transaction->balance_accounts();
}
if($EDIT == 'UPDATESPLITS')
{   
    $transaction->check_all_splits();
}

$account_info = $transaction->getall_account_info($viewall);


include("transactions_main.phtml");


?>
