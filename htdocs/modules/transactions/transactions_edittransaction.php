<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("transactions_include.php");
Framework::XML_authenticate('');//the two includes must be before the authentica, to supply the needed module name for authentication
if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_transactions", "all_transactions")))
{
    header('Content-Type: text/xml');
    echo "<warn>";
    echo "<show>true</show>";
    echo "<message>Permission Denied for Viewing Transaction</message>";
    echo "</warn>";
    exit;
}

//$_POST = $_GET;
$transaction_id = $_POST['transaction_id'];
$ACTION = $_POST['ACTION'];

switch($ACTION)
{
    case "get_transaction":
        $transaction_info = transaction::get_transaction($transaction_id);
        header('Content-Type: text/xml');
        echo "<transaction>";
        echo "<retrieved>true</retrieved>";
        echo "<id>".framework::XML_Replace($transaction_info['transaction_id'])."</id>";
        echo "<comments>".framework::XML_Replace($transaction_info['transaction_comment'])."</comments>";
        echo "<checkno>".$transaction_info['transaction_checkno']."</checkno>";
        echo "<reconcile>".$transaction_info['transaction_reconcile']."</reconcile>";
        echo "<date_year>".substr($transaction_info['transaction_date'], 0 ,4)."</date_year>";
        echo "<date_month>".substr($transaction_info['transaction_date'], 5 ,2)."</date_month>";
        echo "<date_day>".substr($transaction_info['transaction_date'],8 ,2)."</date_day>";
        echo "<r_date_year>".substr($transaction_info['transaction_reconcile_date'], 0 ,4)."</r_date_year>";
        echo "<r_date_month>".substr($transaction_info['transaction_reconcile_date'], 5 ,2)."</r_date_month>";
        echo "<r_date_day>".substr($transaction_info['transaction_reconcile_date'],8 ,2)."</r_date_day>";
        echo "<is_split>".$transaction_info['is_split']."</is_split>";
        $transaction_dc_set = transaction::get_debitcredit_by_transaction($transaction_id);
        foreach($transaction_dc_set as $transaction_dc_line)
        {
            echo 
            "<debit_credit>
                <account>".$transaction_dc_line['transaction_account']."</account>
                <amount>".$transaction_dc_line['transaction_dc_amount']."</amount>
                <dc>".$transaction_dc_line['transaction_dc']."</dc>
                <account_name>".framework::XML_Replace($transaction_dc_line['account_name'])."</account_name>
            </debit_credit>";
        }
        echo "</transaction>";
        exit;
}

?>
