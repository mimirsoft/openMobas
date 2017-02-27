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
if(!($rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "transactions_module")))
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
$transactionAccount = new TransactionAccount($dbh, $FRAMEWORK);
$transaction = new Transaction($dbh, $transactionAccount);


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
        if(!($rbac_user->IsAllowedTo($USER->GetUserID(), "create_account", "transactions_module")))
        {
            echo "PERMISSION DENIED TO CREATE ACCOUNT IN TRANSACTIONS MODULE!!";
            exit;
        }
        if ($account_name != "")
        {
            $transactionAccount->add_accountnew($account_id, $account_name, $account_parent, $accounttype_id, $account_memo, $account_current);
            $account_id = "";
            $account_name = "";
            $accounttype_id = "";
            $account_parent = "";
            $account_memo = "";
        }
        break;
    case "Verify Names":
        $transactionAccount->verify_names();
        break;
    case "Search":
        $results = $transactionAccount->search_accounts($search_string);
        break;
    case "Verify Tree":
        $transactionAccount->verify_tree();
        break;
}

$stmt = $dbh->prepare("SELECT * FROM transactions_accounttype ORDER BY accounttype_name  ");
$stmt->execute();	
$accounttypes_num = $stmt->num_rows();
$accounttypes = $stmt->fetchall_assoc();
$stmt = $dbh->prepare("SELECT COUNT(*) FROM transactions_accounts ");
$stmt->execute();	
$count = $stmt->fetch_assoc();
$account_num = $count['COUNT(*)'];
$page_count = floor($account_num/200);


$account_array = $transactionAccount->build_accountIDtoFullname_array(true);
$starting_point = $page*200;
$account_stack = $transactionAccount->build_account_stack($starting_point, 200);
$account_stack_all = $transactionAccount->build_account_stack_all();

include("transactions_configure_accounts.phtml");

?>






