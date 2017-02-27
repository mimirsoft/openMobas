<?php

/*
 * 
    This file is part of OpenMobas
    Copyright (C) 2011, Kevin Milhoan

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
$WARNING['show'] = false;


if(!($rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "transactions_module")))
{
    echo "PERMISSION DENIED TO ACCESS MODULE TRANSACTIONS!!";
    exit;
}


$transactionAccount = new TransactionAccount($dbh, $FRAMEWORK);
$transaction = new Transaction($dbh, $transactionAccount);

$viewall = '';
@$EDIT = $_POST['EDIT'];
if(@$_GET['VIEWALL'] == "Y")
{
    $viewall='Y';
}	
if($EDIT == 'REBALANCE')
{   
    $transactionAccount->balance_accounts();
}
if($EDIT == 'UPDATESPLITS')
{   
    $transaction->check_all_splits();
}

$account_info = $transactionAccount->getall_account_info($viewall);


include("transactions_main.phtml");


?>
