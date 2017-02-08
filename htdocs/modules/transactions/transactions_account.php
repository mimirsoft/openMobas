<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("transactions_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$date_year = date('Y');
$date_month = date('m');
$date_day = date('d');
$empty = '';
$transaction_checkno = '';
$transaction_comment = '';
$transaction_amount = '';
$transaction_reconcile = 'N';
$working_account = 0;
$rowNumbers = 200;
$TOTAL = 0;
$page = 0;
$RUNNING_TOTAL = 0;
$transactions = '';
$VIEWALL = '';
$WARNING['message'] = '';

if(@$_GET['id'] != '')
{
    $working_account = $_GET['id'];
}
foreach($_GET as $key => $value)
{
    $$key = $value;
}
if($page < 0)
{
    $page = 0;
}

$dbh = new DB_Mysql();

// Here we paginate the transactions
$stmt = $dbh->prepare("SELECT COUNT(*)  
                         FROM transactions_debit_credit 
                        WHERE transaction_account=:1:");
$stmt->execute($working_account);	
$count = $stmt->fetch_assoc();
$account_num = $count['COUNT(*)'];
$page_count = floor($account_num/200);
$starting_point = $page*200;


$tmp2 = "SELECT workingtdc.transaction_dc_amount, workingtdc.transaction_dc, tm.transaction_id, tm.transaction_checkno, tm.transaction_date, tm.transaction_comment, tm.transaction_reconcile, GROUP_CONCAT(odc.transaction_account) AS split
          FROM transactions_debit_credit AS workingtdc
    LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id AND odc.transaction_account != :1:
    INNER JOIN transactions_main AS tm
            ON tm.transaction_id=workingtdc.transaction_id
         WHERE workingtdc.transaction_account = :1:
        GROUP BY transaction_id
      ORDER BY tm.transaction_date, tm.transaction_id ";
if($VIEWALL != 'Y')
{
    $tmp2 .= "LIMIT ".$starting_point.", ".$rowNumbers;
}

$stmt = $dbh->prepare($tmp2);
$stmt->execute($working_account);
$transactions = $stmt->fetchall_assoc();

$tmp = "SELECT SUM(z.transaction_amount) AS debit
          FROM (SELECT transaction_amount, transaction_dc
                  FROM transactions_main 
            INNER JOIN transactions_debit_credit 
                    ON transactions_main.transaction_id=transactions_debit_credit.transaction_id
                 WHERE transactions_debit_credit.transaction_account=:1:
              ORDER BY transaction_date, transactions_main.transaction_id 
                  ASC
                LIMIT $starting_point) 
            AS z
         WHERE z.transaction_dc='DEBIT'";
$stmt = $dbh->prepare($tmp);
$stmt->execute($working_account);
$row = $stmt->fetch_assoc();
$debit_sub = $row['debit'];
$tmp = "SELECT SUM(z.transaction_amount) AS credit
          FROM (SELECT transaction_amount, transaction_dc
                  FROM transactions_main 
            INNER JOIN transactions_debit_credit 
                    ON transactions_main.transaction_id=transactions_debit_credit.transaction_id
                 WHERE transactions_debit_credit.transaction_account=:1:
              ORDER BY transaction_date, transactions_main.transaction_id 
                  ASC
                LIMIT $starting_point) 
            AS z
         WHERE z.transaction_dc='CREDIT'";
$stmt = $dbh->prepare($tmp);
$stmt->execute($working_account);
$row = $stmt->fetch_assoc();
$credit_sub = $row['credit'];
$net = $debit_sub-$credit_sub;
// we need to round after subtracting to avoid machine precision errors. I.E.  $10.60999999
$net = round($net, 2);
$account_info = transaction::get_account_byID($working_account);
if($account_info['accounttype_sign'] == 'CREDIT')
{
    $net = -$net;
}
$debit_or_credit = $account_info['accounttype_sign'];

$TOTAL = transaction::get_balance($working_account);
$RUNNING_TOTAL = bcadd($RUNNING_TOTAL, 0, 2);
$RUNNING_TOTAL = bcadd($RUNNING_TOTAL, $net, 2);

$all_accounts = transaction::build_accountIDtoFullName_array(false);
$account_array = transaction::build_account_stack_all(true);



include("transactions_account.phtml");



?>
