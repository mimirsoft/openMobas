<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");

require_once("transactions_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$action = '';
$transaction = new Transaction($dbh);

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
@$account_reconcileddate = $account_reconcileddate_year."-".$account_reconcileddate_month."-".$account_reconcileddate_day;
@$account_openeddate = $account_openeddate_year."-".$account_openeddate_month."-".$account_openeddate_day;
if ($action == "Save Account" && $account_name != "")
{
    $transaction->update_account($account_id, $account_name, $account_parent, $accounttype_id, $account_memo, $account_current);
    try{
        $transaction->set_account_reconcile_date($account_id, $account_reconcileddate, false);
    }
    catch(TransactionException $exception)
    {
        $WARNING['show'] = true;
        $WARNING['message'] = $exception->message;
        $WARNING['message'] .= "<BR />";
        $WARNING['message'] .= "OVERRIDE RECONCILE DATE?
        <form action=\"$BASE_DIR/transactions/transactions_accountedit.php\"  method=POST>
        <input type=\"SUBMIT\"  name=\"action\"  value=\"Override Reconcile Date\">
        <input type=\"HIDDEN\"  name=\"overridedate\"  value=$account_reconcileddate>
        <input type=\"HIDDEN\"  name=\"account_id\"  value=$account_id>
        </form>";

    }
    $transaction->set_account_open_date($account_id, $account_openeddate);
    $transaction->set_account_flagged($account_id, $account_flagged);
    $transaction->set_account_locked($account_id, $account_locked);
}
if ($action == "Override Reconcile Date")
{
    $transaction->set_account_reconcile_date($account_id, $overridedate, true);
}

if ($action == "Delete Account")
{
    try{
        $transaction->delete_account($account_id);
    }
    catch(MysqlException $e)
    {
        $WARNING['show'] = true;
        $WARNING['message'] = $e->message;
        
    }
}
if ($action == "Reverse All Transactions" && $account_id != "")
{
    try{
        $transaction->reverse_account($account_id);
    }
    catch(TransactionException $e)
    {
        $WARNING['show'] = true;
        $WARNING['message'] = $e->message;
        
    }
    
}

$account = $transaction->get_account_byID($account_id);
$transactions_accounts_parent = $account['account_parent'];
$accounttypes = $transaction->getall_accounttypes();
$account_array = $transaction->build_account_stack_all();

include("transactions_accountedit.phtml");

?>






