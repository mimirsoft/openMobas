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
$WARNING['show'] = false;

@$ACTION = $_POST['ACTION'];
@$account_id = $_POST['account_id'];
@$date_year = $_POST['date_year'];
@$date_month = $_POST['date_month'];
@$date_day = $_POST['date_day'];
@$ending_balance = $_POST['ending_balance'];

$date = $date_year."-".$date_month."-".$date_day;

$query_string = "account_id=".$account_id."&date_year=".$date_year."&date_month=".$date_month."&date_day=".$date_day;

if($ACTION == "Record Reconcile Date")
{
    transactions::set_account_reconcile_date($account_id, $date);
}
if($ACTION == "Record Reconcile")
{
    @$reconcile_credit_list = $_POST['reconcile_credit_list'];
    @$reconcile_debit_list = $_POST['reconcile_debit_list'];
    @$transaction =  $_POST['transaction'];
    if(is_array($reconcile_credit_list))
    {
    foreach($reconcile_credit_list as $transaction_id)
    {
        try{
            $reconciledate = $transaction[$transaction_id]['rdate_year']."-".$transaction[$transaction_id]['rdate_month']."-".$transaction[$transaction_id]['rdate_day'];
            transactions::set_reconcile_date($transaction_id, $reconciledate, false);
        }
        catch(TransactionException $exception)
        {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
        }
        try{
            transactions::toggle_reconcile($transaction_id, 'R', false);
        }
        catch(TransactionException $exception)
        {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            
        }
            
    }
    }
    if(is_array($reconcile_debit_list))
    {
    foreach($reconcile_debit_list as $transaction_id)
    {
        try{
            $reconciledate = $transaction[$transaction_id]['rdate_year']."-".$transaction[$transaction_id]['rdate_month']."-".$transaction[$transaction_id]['rdate_day'];
            transactions::set_reconcile_date($transaction_id, $reconciledate, false);
        }
        catch(TransactionException $exception)
        {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
        }
        try{
            transactions::toggle_reconcile($transaction_id, 'R', false);
        }
        catch(TransactionException $exception)
        {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
        }
            
    }
    }
}
if($ending_balance == '')
{
    $WARNING['show'] = true;
    $WARNING['message'] = "NO ENDING BALANCE, CANNOT RECONCILE";
}

$all_accounts = transactions::build_accountIDtoFullName_array(false);;
$accountTree = transactions::find_subtree_array($account_id);
$subtree_string =  transactions::find_subtree_string($account_id);
$account_info = transactions::get_account_byID($account_id);
$account_reconcile_date = $account_info['account_reconcile_date'];
$r_time = strtotime($account_reconcile_date);
$account_rec_nextday = strtotime("+1 day", $r_time);

$starting_reconciled_balance = transactions::account_total_date_incsub_reconciled($subtree_string, "", $account_reconcile_date, $account_info['accounttype_sign']);

$STARTING_BALANCE = 0;
$runningtotal = $STARTING_BALANCE;



$dbh = new DB_Mysql();


$tmp = "SELECT SUM(z.transaction_dc_amount) AS credit
          FROM (SELECT workingtdc.transaction_dc_amount,
                       workingtdc.transaction_account,
                       workingtdc.transaction_dc,
                       tm.transaction_reconcile,
                       tm.transaction_id
                  FROM transactions_debit_credit AS workingtdc
             LEFT JOIN transactions_debit_credit AS odc
                    ON workingtdc.transaction_id=odc.transaction_id
            INNER JOIN transactions_main AS tm
                 ON tm.transaction_id=workingtdc.transaction_id
                 WHERE (workingtdc.transaction_account IN (SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $account_info[account_left] AND $account_info[account_right])  
                       AND odc.transaction_account NOT IN (SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $account_info[account_left] AND $account_info[account_right]) )
                   AND transaction_reconcile_date <= '$account_reconcile_date'
              GROUP BY tm.transaction_id) 
            AS z
         WHERE z.transaction_dc='CREDIT'
           AND z.transaction_reconcile = 'R'";

$stmt = $dbh->prepare($tmp);
$stmt->execute();
$row = $stmt->fetch_assoc();
$income_sub = $row['credit'];


$tmp = "SELECT SUM(z.transaction_dc_amount) AS debit
          FROM (SELECT workingtdc.transaction_dc_amount,
                       workingtdc.transaction_account,
                       workingtdc.transaction_dc,
                       tm.transaction_reconcile,
                       tm.transaction_id
                  FROM transactions_debit_credit AS workingtdc
             LEFT JOIN transactions_debit_credit AS odc
                    ON workingtdc.transaction_id=odc.transaction_id
            INNER JOIN transactions_main AS tm
                 ON tm.transaction_id=workingtdc.transaction_id
                 WHERE (workingtdc.transaction_account IN (SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $account_info[account_left] AND $account_info[account_right])  
                       AND odc.transaction_account NOT IN (SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $account_info[account_left] AND $account_info[account_right]) )
                   AND transaction_reconcile_date <= '$account_reconcile_date'
              GROUP BY tm.transaction_id) 
            AS z
         WHERE z.transaction_dc='DEBIT'
           AND z.transaction_reconcile = 'R'";

$stmt = $dbh->prepare($tmp);
$stmt->execute();
$row = $stmt->fetch_assoc();
$expense_sub = $row['debit'];


$STARTING_BALANCE = 0;
$RUNNING_TOTAL = 0;

if($account_info['accounttype_sign'] == 'CREDIT')
{
    $net = $income_sub-$expense_sub;
}
if($account_info['accounttype_sign'] == 'DEBIT')
{
    $net = $expense_sub-$income_sub;
}

$RUNNING_TOTAL = bcadd($RUNNING_TOTAL, $net, 2);
$runningtotal = $RUNNING_TOTAL;
$TOTAL = transactions::get_balance($account_id);
//get all unreconciled transactions, and all reconciled transactions after account reconciled date and before target date

$tmp = "SELECT workingtdc.transaction_dc, 
               workingtdc.transaction_id, 
               workingtdc.transaction_dc_amount, 
               workingtdc.transaction_account, 
               tm.transaction_date, 
               tm.transaction_checkno, 
               tm.transaction_comment, 
               tm.transaction_reconcile, 
               tm.transaction_reconcile_date, 
               GROUP_CONCAT(odc.transaction_account) AS split
          FROM transactions_debit_credit AS workingtdc
     LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id
    INNER JOIN transactions_main AS tm
            ON tm.transaction_id=workingtdc.transaction_id
         WHERE (workingtdc.transaction_account IN (SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $account_info[account_left] AND $account_info[account_right])  
               AND odc.transaction_account NOT IN (SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $account_info[account_left] AND $account_info[account_right]) )
           AND ((transaction_reconcile != 'R' 
                 AND transaction_date <= :1: )
               OR
               (transaction_reconcile = 'R' 
                 AND transaction_reconcile_date > :2:
                 AND transaction_reconcile_date <= :1:)
               )
           AND workingtdc.transaction_dc='DEBIT'
      GROUP BY tm.transaction_id
      ORDER BY transaction_reconcile DESC, transaction_reconcile_date ASC, transaction_date ASC, transaction_checkno ASC";

$stmt = $dbh->prepare($tmp);
$stmt->execute($date, $account_reconcile_date);
$debits = $stmt->fetchall_assoc();

$tmp = "SELECT workingtdc.transaction_dc, 
               workingtdc.transaction_id, 
               workingtdc.transaction_dc_amount, 
               workingtdc.transaction_account, 
               tm.transaction_date, 
               tm.transaction_checkno, 
               tm.transaction_comment, 
               tm.transaction_reconcile, 
               tm.transaction_reconcile_date, 
               GROUP_CONCAT(odc.transaction_account) AS split
          FROM transactions_debit_credit AS workingtdc
     LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id
    INNER JOIN transactions_main AS tm
            ON tm.transaction_id=workingtdc.transaction_id
         WHERE (workingtdc.transaction_account IN (SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $account_info[account_left] AND $account_info[account_right])  
               AND odc.transaction_account NOT IN (SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $account_info[account_left] AND $account_info[account_right]) )
           AND ((transaction_reconcile != 'R' 
                 AND transaction_date <= :1: )
               OR
               (transaction_reconcile = 'R' 
                 AND transaction_reconcile_date > :2:
                 AND transaction_reconcile_date <= :1:)
               )
		  AND workingtdc.transaction_dc='CREDIT'
      GROUP BY tm.transaction_id
      ORDER BY transaction_reconcile DESC, transaction_reconcile_date ASC, transaction_date ASC, transaction_checkno ASC";

$stmt = $dbh->prepare($tmp);
$stmt->execute($date, $account_reconcile_date);
$credits = $stmt->fetchall_assoc();

// while($dbRow2 = $stmt->fetch_assoc())
// {
//     $dbRow2['record_year'] = substr($dbRow2['transaction_date'], 0 ,4);
//     $dbRow2['record_month'] = substr($dbRow2['transaction_date'], 5 ,2);
//     $dbRow2['record_day'] = substr($dbRow2['transaction_date'], 8 ,2);
//     if($dbRow2['transaction_reconcile_date'] == NULL)
//     {
//         $dbRow2['reconcile_year'] = "0000";
//         $dbRow2['reconcile_month'] = "00";
//         $dbRow2['reconcile_day'] = "00";
//     }
//     else
//     {
//         $dbRow2['reconcile_year'] = substr($dbRow2['transaction_reconcile_date'], 0 ,4);
//         $dbRow2['reconcile_month'] = substr($dbRow2['transaction_reconcile_date'], 5 ,2);
//         $dbRow2['reconcile_day'] = substr($dbRow2['transaction_reconcile_date'], 8 ,2);
// 
//     }
//     if ($dbRow2['transaction_dc'] = 'DEBIT') // if it is in the accountTree
//     {
//         if($dbRow2['transaction_reconcile'] == "R")
//         {
//             if($account_info['accounttype_sign'] == 'CREDIT')
//             {
//                 $runningtotal = bcsub($runningtotal, $dbRow2['transaction_amount'], 2);
//                 $dbRow2['color'] = "class=\"neg\"";
// 
//             }
//             else
//             {
//                 $runningtotal = bcadd($runningtotal, $dbRow2['transaction_amount'], 2);
//                 $dbRow2['color'] = "";
//             }
//         }
//         else
//         {
//             $dbRow2['color'] = "";
//         }
//         $dbRow2['runningtotal'] = $runningtotal;
//     }
//     if ($dbRow2['transaction_dc'] = 'CREDIT')
//     {
//         if($dbRow2['transaction_reconcile'] == "R")
//         {
//             if($account_info['accounttype_sign'] == 'CREDIT')
//             {
//                 $runningtotal = bcadd($runningtotal, $dbRow2['transaction_amount'], 2);
//                 $dbRow2['color'] = "";
// 
//             }
//             else
//             {
//                 $runningtotal = bcsub($runningtotal, $dbRow2['transaction_amount'], 2);
//                 $dbRow2['color'] = "class=\"neg\"";
//             }
//         }
//         else
//         {
//             $dbRow2['color'] = "";
//         }
// 
//         $dbRow2['runningtotal'] = $runningtotal;
//     }
// $transactions[] = $dbRow2;
// }

include("transactions_reconcile3.phtml");


?>

