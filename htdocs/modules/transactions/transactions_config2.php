<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include("../../../framework_masterinclude.php");
include("transactions_include.php");
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
$transactions_accounttype_ID2 = 'NULL';
$transactions_accounttype_name = '';
$transactions_accounttype_sign = '';
$EDIT = '';
$ACTION = '';
$page = 0;
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = "01";
$override = '';

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
if($EDIT == 'CONVERT DEBIT/CREDIT')
{   
    transaction::convert_debitcredit();
}
if($ACTION == 'CLOSE YEAR END INCOME AND EXPENSE')
{   
    $row = transaction::get_accounttype_by_name('INCOME');
    $income_set = transaction::getall_accounts_by_type($row['accounttype_id']);
    $date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;
    foreach($income_set as $income_account)
    {
        
        $total = transaction::account_total_date($income_account['account_id'], "", $date, 'CREDIT');
        if($total > 0)
        {
            $dc_line['transaction_dc'] = 'CREDIT';
            $dc_line['transaction_dc_amount'] = abs($total);
            $dc_line['transaction_account'] = $account_id;
            $dc_set[] = $dc_line;
            $dc_line['transaction_dc'] = 'DEBIT';
            $dc_line['transaction_dc_amount'] = abs($total);
            $dc_line['transaction_account'] = $income_account['account_id'];
            $dc_set[] = $dc_line;
            $comment2 = "YEAR END- ZERO OUT TO PROFIT AND LOSS";;
            $transaction_id = transactions::add_transaction('NULL', $date, $comment2, "", $dc_set, $override);
            unset($dc_set);
        }
        elseif($total < 0)
        {
            $dc_line['transaction_dc'] = 'CREDIT';
            $dc_line['transaction_dc_amount'] = abs($total);
            $dc_line['transaction_account'] = $income_account['account_id'];
            $dc_set[] = $dc_line;
            $dc_line['transaction_dc'] = 'DEBIT';
            $dc_line['transaction_dc_amount'] = abs($total);
            $dc_line['transaction_account'] = $account_id;
            $dc_set[] = $dc_line;
            $comment2 = "YEAR END- ZERO OUT TO PROFIT AND LOSS";;
            $transaction_id = transactions::add_transaction('NULL', $date, $comment2, "", $dc_set, $override);
            unset($dc_set);
        }
    }

    $row = transaction::get_accounttype_by_name('EXPENSE');
    $expense_set = transaction::getall_accounts_by_type($row['accounttype_id']);
    foreach($expense_set as $expense_account)
    {
        
        $total = transaction::account_total_date($expense_account['account_id'], "", $date, 'DEBIT');
        if($total < 0)
        {
            $dc_line['transaction_dc'] = 'CREDIT';
            $dc_line['transaction_dc_amount'] = abs($total);
            $dc_line['transaction_account'] = $account_id;
            $dc_set[] = $dc_line;
            $dc_line['transaction_dc'] = 'DEBIT';
            $dc_line['transaction_dc_amount'] = abs($total);
            $dc_line['transaction_account'] = $expense_account['account_id'];
            $dc_set[] = $dc_line;
            $comment2 = "YEAR END- ZERO OUT TO PROFIT AND LOSS";;
            $transaction_id = transaction::add_transaction('NULL', $date, $comment2, "", $dc_set, $override);
            unset($dc_set);
        }
        elseif($total > 0)
        {
            $dc_line['transaction_dc'] = 'CREDIT';
            $dc_line['transaction_dc_amount'] = abs($total);
            $dc_line['transaction_account'] = $expense_account['account_id'];
            $dc_set[] = $dc_line;
            $dc_line['transaction_dc'] = 'DEBIT';
            $dc_line['transaction_dc_amount'] = abs($total);
            $dc_line['transaction_account'] = $account_id;
            $dc_set[] = $dc_line;
            $comment2 = "YEAR END- ZERO OUT TO PROFIT AND LOSS";;
            $transaction_id = transaction::add_transaction('NULL', $date, $comment2, "", $dc_set, $override);
            unset($dc_set);
        }
    }
}

switch($action)
{
    case "Save Account Type":
        if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "configure_module", "transactions_module")))
        {
            echo "PERMISSION DENIED TO CREATE ACCOUNT IN TRANSACTIONS MODULE!!";
            exit;
        }
	transaction::add_accounttype($transactions_accounttype_ID2, $transactions_accounttype_name, $transactions_accounttype_sign);
	$transactions_accounttype_ID2 = "NULL";
	$transactions_accounttype_name = "";
	$transactions_accounttype_sign = "";
        break;
    case "Edit Account Type":
        if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "configure_module", "transactions_module")))
        {
            echo "PERMISSION DENIED TO CREATE ACCOUNT IN TRANSACTIONS MODULE!!";
            exit;
        }
	$accttype_info = transaction::get_accounttype_by_id($transactions_accounttype_edit);
        $transactions_accounttype_name = $accttype_info['accounttype_name'];
	$transactions_accounttype_ID2 = $accttype_info['accounttype_id'];
	$transactions_accounttype_sign = $accttype_info['accounttype_sign'];
        break;
}
$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT * FROM transactions_accounttype ORDER BY accounttype_name  ");
$stmt->execute();	
$accounttypes_num = $stmt->num_rows();
$accounttypes = $stmt->fetchall_assoc();

$account_array = transaction::build_account_stack_all(false);

include("transactions_config2.phtml");


?>






