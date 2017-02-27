<?php
/*
 *
 This file is part of OpenMobas
 Copyright (C) 2011, Kevin Milhoan, Mimir Software Corporation

 OpenMobas is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 OpenMobas is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

 Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

 *
 */

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

$transactionAccount = new TransactionAccount($dbh, $FRAMEWORK);
$transaction = new Transaction($dbh, $transactionAccount);


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
$account_info = $transactionAccount->get_account_byID($working_account);
if($account_info['accounttype_sign'] == 'CREDIT')
{
    $net = -$net;
}
$debit_or_credit = $account_info['accounttype_sign'];

$TOTAL = $transactionAccount->get_balance($working_account);
$RUNNING_TOTAL = bcadd($RUNNING_TOTAL, 0, 2);
$RUNNING_TOTAL = bcadd($RUNNING_TOTAL, $net, 2);

$all_accounts = $transactionAccount->build_accountIDtoFullName_array(false);
$account_array = $transactionAccount->build_account_stack_all(true);



include("transactions_account.phtml");



?>
