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


//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("transactions_include.php");
$FRAMEWORK->XML_authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!($rbac_user->IsAllowedTo($USER->GetUserID(), "reconcile_accounts", "transactions_module")))
{
    exit;
}
if(@$_GET['override'] == "true")
{
    //$_POST = $_GET;
}

@$date_year =  $_POST['date_year'];
@$date_month =  $_POST['date_month'];
@$date_day =  $_POST['date_day'];
@$reconciledate_year =  $_POST['reconciledate_year'];
@$reconciledate_month = $_POST['reconciledate_month'];
@$reconciledate_day =  $_POST['reconciledate_day'];
@$checkno =  $_POST['checkno'];
@$comment =  $_POST['comment'];
@$transaction_id = $_POST['transaction_id'];
@$reconcile = $_POST['reconcile'];;
@$ACTION = $_POST['ACTION'];;
@$dc = $_POST['dc'];
@$account = $_POST['account'];
@$amount =  $_POST['amount'];
@$from_act  =  $_POST['from_account'];
@$to_act  =  $_POST['to_account'];
@$transaction_multi  =  $_POST['transaction_multi'];
$override = false;
$dc_set = array();
if(is_array($account))
{    
    foreach($account as $key => $value)
    {
        $dc_line['transaction_dc'] = $dc[$key];
        $dc_line['transaction_dc_amount'] = $amount[$key];
        $dc_line['transaction_account'] = $account[$key];
        $dc_set[] = $dc_line;
    }
}
if($rbac_user->IsAllowedTo($USER->GetUserID(), "override_transactions", "all_transactions"))
{
    if(isset($_POST['override'] ))
    {
        $override = $_POST['override'];
    }
}


$transactionAccount = new TransactionAccount($dbh, $FRAMEWORK);
$transaction = new Transaction($dbh, $transactionAccount);


switch($ACTION)
{
    case "save_transaction":
        $date = $date_year."-".$date_month."-".$date_day;
        $reconciledate = $reconciledate_year."-".$reconciledate_month."-".$reconciledate_day;
        try{
            $transaction_id = $transaction->add_transaction($transaction_id, $date, $comment, $checkno, $dc_set, $override);
        }
        catch(TransactionException $exception)
        {
            header('Content-Type: text/xml');
            echo "<warn>";
            echo "<show>true</show>";
            echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
            echo "</warn>";
            exit;
        }
        if(isset($reconcile) && $reconciledate != "--")
        {
            try{
                $transaction->set_reconcile_date($transaction_id, $reconciledate, $override);
            }
            catch(TransactionException $exception)
            {
                header('Content-Type: text/xml');
                echo "<warn>";
                echo "<show>true</show>";
                echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
                echo "</warn>";
                exit;
            }
            try{
                $transaction->toggle_reconcile($transaction_id, $reconcile, $override);
            }
            catch(TransactionException $exception)
            {
                header('Content-Type: text/xml');
                echo "<warn>";
                echo "<show>true</show>";
                echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
                echo "</warn>";
                exit;
            }
        }
        else
        {
            header('Content-Type: text/xml');
            echo "<warn>";
            echo "<transaction>SAVED</transaction>";
            echo "<show>true</show>";
            echo "<message>TRANSACTION SAVED, BUT RECONCILE OR RECONCILE DATE NOT SET AND SKIPPED</message>";
            echo "</warn>";
            exit;
        }
        break;
    case "delete_transaction":
            try{
                $transaction->delete_transaction($transaction_id,  $override);
            }
            catch(TransactionException $exception)
            {
                header('Content-Type: text/xml');
                echo "<warn>";
                echo "<show>true</show>";
                echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
                if($exception->override)
                {
                    echo "<override>true</override>";
                }
                echo "</warn>";
                exit;
            }
            break;
    case "delete_transaction_multi":
        foreach($transaction_multi as $delete_tid)
        {
            try{
                $transaction->delete_transaction($delete_tid,  $override);
            }
            catch(TransactionException $exception)
            {
                header('Content-Type: text/xml');
                echo "<warn>";
                echo "<show>true</show>";
                echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
                if($exception->override)
                {
                    echo "<override>true</override>";
                }
                echo "</warn>";
                exit;
            }
            
        }
        break;
    case "move_transaction":
        try{
            $transaction->move_transaction($transaction_id, $from_act, $to_act);
        }
        catch(TransactionException $exception)
        {
            header('Content-Type: text/xml');
            echo "<warn>";
            echo "<show>true</show>";
            echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
            if($exception->override)
            {
                echo "<override>true</override>";
            }
            echo "</warn>";
            exit;
        }
        break;
    case "move_transaction_multi":
        foreach($transaction_multi as $transaction_id)
        {
            try{
                $transaction->move_transaction($transaction_id, $from_act, $to_act);
            }
            catch(TransactionException $exception)
            {
                header('Content-Type: text/xml');
                echo "<warn>";
                echo "<show>true</show>";
                echo "<transaction>".$exception->transaction_id."</transaction>";
                echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
                if($exception->override)
                {
                    echo "<override>true</override>";
                }
                echo "</warn>";
                exit;
            }
        }
        break;
    case "toggle_rec":
        $reconciledate = $reconciledate_year."-".$reconciledate_month."-".$reconciledate_day;
        if(isset($reconcile) && $reconciledate != "--")
        {
            try{
                $transaction->set_reconcile_date($transaction_id, $reconciledate, $override);
            }
            catch(TransactionException $exception)
            {
                header('Content-Type: text/xml');
                echo "<warn>";
                echo "<show>true</show>";
                echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
                echo "</warn>";
                exit;
            }
            try{
                $transaction->toggle_reconcile($transaction_id, $reconcile, $override);
            }
            catch(TransactionException $exception)
            {
                header('Content-Type: text/xml');
                echo "<warn>";
                echo "<show>true</show>";
                echo "<message>".$FRAMEWORK->XML_Replace($exception->message)."</message>";
                echo "</warn>";
                exit;
            }
        }
        else
        {
            header('Content-Type: text/xml');
            echo "<warn>";
            echo "<show>true</show>";
            echo "<message> RECONCILE OR RECONCILE DATE VALUE NOT SET.</message>";
            echo "</warn>";
            exit;

        }
        break;
}
header('Content-Type: text/xml');
echo "<transaction>SAVED</transaction>";
            


?>
