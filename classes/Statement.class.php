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
 along with OpenMobas.  If not, see <http://www.gnu.org/licenses/>.

 Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

 *
*This is the statement class file
**
*/
//error_reporting(E_ALL);
//ini_set('display_errors', '1');


class StatementException extends Exception{
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}




class Statement {

public static function save_statement($id, $name, $array, $priv)
{
    $dbh = new DB_Mysql();
    $array = serialize($array);//serializing the array may cause issues with the :n:
    if($id == "NULL")// If it is a new entry.
    {
        $stmt = $dbh->prepare("INSERT INTO statements_main 
                                       SET statement_id=:1:, 
                                           statement_name=:2:, 
                                           statement_privilege=:3:, 
                                           statement_array=:4:");
    }
    else//If it is an edit to an existing entry
    {
        $stmt = $dbh->prepare("UPDATE statements_main 
                                  SET statement_name=:2:, 
                                      statement_privilege=:3:,
                                      statement_array=:4: 
                               WHERE statement_id=:1:");
    }
    $stmt->execute($id, $name, $priv, $array);
    return mysql_insert_id();
}
public static function get_statement_by_id($id)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM statements_main WHERE statement_id=:1:");
    $stmt->execute($id);	
    $statement_object = $stmt->fetch_assoc();
    return $statement_object;
}
public static function delete_statement($id)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("DELETE FROM statements_main 
                                     WHERE statement_id=:1:");
    $stmt->execute($id);	
}

public static function getall_public_statements()
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM statements_main  WHERE statement_privilege = 'PUBLIC' ORDER BY statement_name");
    $stmt->execute();	
    return  $stmt->fetchall_assoc();
}
public static function getall_private_statements()
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM statements_main  WHERE statement_privilege = 'PRIVATE' ORDER BY statement_name");
    $stmt->execute();	
    return  $stmt->fetchall_assoc();
}
public static function getall_statements()
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM statements_main ORDER BY statement_name");
    $stmt->execute();	
    return  $stmt->fetchall_assoc();
}

function date_builder($dates)
{
    $startdate = $dates['start_year']."-".$dates['start_month']."-".$dates['start_day'];
    $enddate = $dates['end_year']."-".$dates['end_month']."-".$dates['end_day'];
    $prestartdate_month = $dates['start_month'];
    $prestartdate_year = $dates['start_year'];
    $prestartdate_day = $dates['start_day']-1;
    if($prestartdate_day == 0)
    {
        $prestartdate_day = 31;
        $prestartdate_month--;
    }
    $prestartdate_day = "0".$prestartdate_day;
    if($prestartdate_month == 0)
    {
        $prestartdate_month =12;
        $prestartdate_year--;
    }
    $prestartdate = $prestartdate_year."-".$prestartdate_month."-".$prestartdate_day;
    
    $dates['start'] = $startdate;
    $dates['end'] = $enddate;
    $dates['pre'] = $prestartdate;
    $start_timestamp = strtotime($startdate);
    $end_timestamp = strtotime($enddate);

    $months = date("m",$end_timestamp) - date( "m", $start_timestamp) + (date("Y",$end_timestamp) - date( "Y", $start_timestamp)) *12;
    $dates['month_count'] = $months+1;
    
    return $dates;
}

function aging_summary($account_id, $target, $date, $accountIDtoNameArray, $sign)//not working
{
    $date_time = strtotime($date);
    $date30_time = strtotime("-30 days", $date_time);
    $date31_time = strtotime("-31 days", $date_time);
    $date60_time = strtotime("-60 days", $date_time);
    $date61_time = strtotime("-61 days", $date_time);
    $date90_time = strtotime("-90 days", $date_time);
    $date91_time = strtotime("-91 days", $date_time);
    //aging
    $row['from_account'] = $accountIDtoNameArray[$account_id];
    $row['as_currentowed'] = "0.00";
    $row['as_030owed'] = "0.00";
    $row['as_3060owed'] = "0.00";
    $row['as_6090owed'] = "0.00";
    $row['as_o90owed'] = "0.00";
    $row['as_prepay'] = "0.00";
    $row['as_total'] = "0.00";

    $balance = transaction::account_total_date($account_id, "", $date, $sign);
    if($balance > 0  || $balance < 0)
    {
        $row['as_total'] = $balance;
        if($balance > 0)
        {
            $row['as_currentowed'] = $balance;
            $net30 = statement::retrieve_expense($account_id, "ALL", date("Y-m-d", $date30_time), $date);
            $remainder = $balance - $net30;
            if($remainder < 0 )
            {
                $net30 =  $balance;
                $remainder = 0;
            }
            $row['as_030owed'] = $net30;
        }
        if($balance < 0)
        {
            $row['as_prepay'] = -$balance;
            $remainder = 0;
        }
        if($remainder != 0)
        {
            $net60 = statement::retrieve_expense($account_id, "ALL", date("Y-m-d", $date60_time),  date("Y-m-d", $date31_time));
            $remainder60 = $remainder - $net60;
            if($remainder60 < 0 )
            {
                $net60 =  $remainder;
                $remainder60 = 0;
            }
            $row['as_3060owed'] =  $net60;
            if($remainder60 != 0)
            {
                $net90 = statement::retrieve_expense($account_id, "ALL",  date("Y-m-d", $date90_time),  date("Y-m-d", $date61_time));
                $remainder90 = $remainder60 - $net90;
                if($remainder90 < 0 )
                {
                    $net90 =  $remainder60;
                    $remainder90 = 0;
                }
                $row['as_6090owed'] = $net90;
                if($remainder90 != 0)
                {
                    $row['as_o90owed'] = $remainder90;
                
                }
            }
        }

    }
    return $row;        

}



function bank_reconcile($account_string, $account_subtree_array, $start, $end, $accountIDtoNameArray, $accountIDtoFullNameArray, $credit_debit)
{
    $start_time = strtotime($start);
    $prestart_time = strtotime("-1 days", $start_time);
    //get the starting balance, without sub accounts
    // $starting_balance = transaction::account_total_date_incsub($account, "", date("Y-m-d", $prestart_time), $account_info['accounttype_sign']);
    $cleared_starting = transaction::account_total_date_incsub_reconciled($account_string, "", date("Y-m-d", $prestart_time), $credit_debit);
    $cleared_additions = transaction::income_total_date_incsub_reconciled($account_string, $start, $end, $credit_debit);
    $cleared_subtractions = transaction::expense_total_date_incsub_reconciled($account_string, $start, $end, $credit_debit);
    if($credit_debit == "R")
    {
        $cleared_starting = bcmul($cleared_starting, -1, 2);
        $cleared_a = $cleared_subtractions;
        $cleared_s = $cleared_additions;
    }
    else{
        $cleared_a = $cleared_additions;
        $cleared_s = $cleared_subtractions;
        $cleared_starting = bcmul($cleared_starting, 1, 2);

    }
    $cleared_a = bcmul($cleared_a, 1, 2);
    $cleared_s = bcmul($cleared_s, 1, 2);
    $cleared_ending = $cleared_starting+$cleared_a-$cleared_s;
    
    //get all transactions between start and end
    $line['transaction_date'] = "";
    $line['from_or_to_account'] = "";
    $line['debit_amount'] = "";
    $line['sign'] = "";
    $line['transaction_comment'] = "";
    $line['credit_amount'] = "";
    $line['running_total'] = '';
    $line['transaction_checkno'] = "ACCOUNT SUMMARY";
    $line['transaction_checkno_colspan'] = "3";
    $data_row[] = $line;
    $line['transaction_date'] = "Beginning Balance";
    $line['transaction_date_colspan'] = "1";
    $line['from_or_to_account'] = "";
    $line['debit_amount'] = "";
    $line['sign'] = "";
    $line['transaction_comment'] = "";
    $line['credit_amount'] = "";
    $line['transaction_amount'] = "";
    $line['running_total'] = $cleared_starting;
    $line['transaction_checkno'] = "";
    $line['transaction_checkno_colspan'] = "";
    $data_row[] = $line;
    $line['transaction_date'] = "Additions";
    $line['transaction_date_colspan'] = "2";
    $line['running_total'] = $cleared_a;
    $data_row[] = $line;
    $line['transaction_date'] = "Subtractions";
    $line['transaction_date_colspan'] = "2";
    $line['running_total'] = $cleared_s;
    $data_row[] = $line;
    $line['transaction_date'] = "Ending Balance";
    $line['transaction_date_colspan'] = "2";
    $line['running_total'] = $cleared_ending;
    $data_row[] = $line;
    $line['transaction_date'] = "&nbsp";
    $line['running_total'] = "";
    $data_row[] = $line;
    $line['transaction_date'] = "";
    $line['from_or_to_account'] = "";
    $line['debit_amount'] = "";
    $line['sign'] = "";
    $line['transaction_comment'] = "";
    $line['credit_amount'] = "";
    $running_total = $cleared_starting;
    $transactions = statement::retrieve_all_transactions_debits_reconciled($account_string, $start, $end, $account_subtree_array, $running_total, $accountIDtoNameArray, 1, $credit_debit, "RECONCILED", "RECONCILE_DATE");
    if(count($transactions) > 0)
    {
        $line['running_total'] = $cleared_starting;
        $line['transaction_checkno_colspan'] = "3";
        $line['transaction_checkno'] = "RECONCILED DEPOSITS";
        $data_row[] = $line;
        
        $line['transaction_checkno_colspan'] = "0";
        $data_row = array_merge($data_row, $transactions);
        $row = array_pop($transactions);
        if(isset($row['running_total']))
        {
            $running_total = $row['running_total'];
        }
    }    
    $transactions = statement::retrieve_all_transactions_credits_reconciled($account_string, $start, $end, $account_subtree_array, $running_total, $accountIDtoNameArray, 1, $credit_debit, "RECONCILED", "RECONCILE_DATE");
    if(count($transactions) > 0)
    {
        $line['transaction_checkno'] = "&nbsp";
        $line['running_total'] = "";
        $data_row[] = $line;
        $line['transaction_checkno_colspan'] = "3";
        $line['transaction_checkno'] = "RECONCILED CHECKS";
        $line['running_total'] = $running_total;
        $data_row[] = $line;
        $line['transaction_checkno_colspan'] = "0";
        
        $data_row = array_merge($data_row, $transactions);
        $row = array_pop($transactions);
        if(isset($row['running_total']))
        {
            $running_total = $row['running_total'];
        }
    }
    $transactions = statement::retrieve_all_transactions_debits_unreconciled($account_string, $start, $end, $account_subtree_array, $running_total, $accountIDtoNameArray, 1, $credit_debit, "", "CHECKNO");
    if(count($transactions) > 0)
    {
        $line['transaction_checkno'] = "&nbsp";
        $line['running_total'] = "";
        $data_row[] = $line;
        $line['transaction_checkno_colspan'] = "3";
        $line['transaction_checkno'] = "UNRECONCILED DEPOSITS";
        $line['running_total'] = $running_total;
        $data_row[] = $line;
        $line['transaction_checkno_colspan'] = "0";
        $data_row = array_merge($data_row, $transactions);
        $row = array_pop($transactions);
        if(isset($row['running_total']))
        {
            $running_total = $row['running_total'];
        }
    }

    $transactions = statement::retrieve_all_transactions_credits_unreconciled($account_string, $start, $end, $account_subtree_array, $running_total, $accountIDtoNameArray, 1, $credit_debit, "", "CHECKNO");
    if(count($transactions) > 0)
    {
        $line['transaction_checkno'] = "&nbsp";
        $line['running_total'] = "";
        $data_row[] = $line;
        $line['transaction_checkno_colspan'] = "3";
        $line['transaction_checkno'] = "UNRECONCILED CHECKS";
        $line['running_total'] = $running_total;
        $data_row[] = $line;
        $data_row = array_merge($data_row, $transactions);
        $line['transaction_checkno'] = "&nbsp";
        $line['running_total'] = "";
        $data_row[] = $line;
    }

    //package the lines
    return $data_row;

}

function build_dset_ledger($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray)
{
    $aset_array = transaction::getall_account_info('Y');
    foreach($aset_array as $account_info)
    {
        //get the starting balance, without sub accounts
        $starting_balance = transaction::account_total_date($account_info['account_id'], "", $date['pre'], $account_info['accounttype_sign']);
        $account_subtree_array = transaction::find_subtree_array($account_info['account_id']);
        //get all transactions between start and end
        $line['from_or_to_account'] = $accountIDtoFullNameArray[$account_info['account_id']];
        $line['from_or_to_account_colspan'] = "4";
        $line['transaction_date'] = "";
        $line['transaction_checkno'] = "";
        $line['transaction_comment'] = "";
        $line['debit_amount'] = "";
        $line['credit_amount'] = "";
        $line['running_total'] = $starting_balance."=Starting Balance";
        $data_row[] = $line;
        unset($line['from_or_to_account_colspan']);
        $transactions = statement::retrieve_all_transactions_net($account_info['account_id'], $date['start'], $date['end'], $account_subtree_array, $starting_balance, $accountIDtoNameArray, "1", $account_info['accounttype_sign'], $drow['data_set_sort'], $BASE_DIR);
        $data_row = array_merge($data_row, $transactions);
        $ending_balance = transaction::account_total_date($account_info['account_id'], "", $date['end'], $account_info['accounttype_sign']);
        $net = $starting_balance-$ending_balance;
        $net = number_format($net, 2);
        $line['from_or_to_account'] = "NetChange=".$net;
        $line['running_total'] = $ending_balance;
        $data_row[] = $line;
        $line['from_or_to_account'] = "&nbsp";
        $line['running_total'] = "";
        $data_row[] = $line;
        //package the lines
    }
    $data_set = array("transaction_lines" => $data_row,
                      "set_meta_data" => array());

    return $data_set;
}

function build_table_rows($data_row, $display_row, $meta_data)
{
    foreach($display_row as $element_cell)
    {
        switch($element_cell['type'])
        {
            case "date":
                $cell['text'] = $data_row['transaction_date'];
                $cell['class'] = "itemized_date";
                if(isset($data_row["transaction_date_colspan"]))
                {
                    $cell['colspan'] = $data_row["transaction_date_colspan"];
                }
            break;
            case "reconcile_date":
                $cell['text'] = @$data_row['transaction_reconcile_date'];
                $cell['class'] = "itemized_date";
                if(isset($data_row["transaction_reconcile_date_colspan"]))
                {
                    $cell['colspan'] = $data_row["transaction_reconcile_date_colspan"];
                }
            break;
            case "comment":
                $cell['text'] = $data_row['transaction_comment'];
                $cell['class'] = "itemized_comment";
            break;
            case "fillin_label":
                $cell['text'] = $element_cell['fillin'];
                $cell['class'] = "label_fillin";
            break;
            case "check_no":
                if($data_row['transaction_checkno'] == "0")
                {
                    $cell['text'] = '&nbsp';
                }
                else
                {
                    $cell['text'] = $data_row['transaction_checkno'];
                }
                if(isset($data_row["transaction_checkno_colspan"]))
                {
                    $cell['colspan'] = $data_row["transaction_checkno_colspan"];
                }
                $cell['class'] = "itemized_checkno";
            break;
            case "running_total":
                $cell['text'] = "";
                if($data_row['running_total'] != "")
                {
                    $cell['text'] = number_format($data_row['running_total'], 2);
                }
                $cell['class'] = "itemized_running_total";
            break;
            case "from_account":
                $cell['text'] = $data_row['from_account'];
                $cell['class'] = "itemized_target_account";
                if(isset($data_row['cell_override']['from_account']))
                {
                    $cell['class'] = $data_row['cell_override']['from_account'];
                }
            break;
            case "to_account":
                $cell['text'] = $data_row['to_account'];
                $cell['class'] = "itemized_target_account";
            break;
            case "from_or_to_account":
                $cell['text'] = $data_row['from_or_to_account'];
                $cell['class'] = "itemized_target_account";
                if(isset($data_row["from_or_to_account_colspan"]))
                {
                    $cell['colspan'] = $data_row["from_or_to_account_colspan"];
                }
            break;
            case "amount":
                switch($data_row['sign'])
                {
                    case "positive":
                        $cell['text'] = $data_row['transaction_dc_amount'];
                        $cell['class'] = "itemized_amount";
                    break;
                    case "negative":
                        $cell['text'] = "-".$data_row['transaction_dc_amount'];
                        $cell['class'] = "itemized_amount_negative";
                    break;
                    default:
                        $cell['text'] = @$data_row['transaction_dc_amount'];
                        $cell['class'] = "itemized_amount";
                    break;
                }
            break;
            case "debit":
                $cell['text'] = $data_row['debit_amount'];
                $cell['class'] = "debit_amount";
            break;
            case "credit":
                $cell['text'] = $data_row['credit_amount'];
                $cell['class'] = "credit_amount";
            break;
            case "balance":
                 if($data_row['balance'] == "")
                {
                    $cell['text'] = "";
                    break;
                }
               switch($data_row['sign'])
                {
                    case "positive":
                        $cell['text'] = "$".number_format($data_row['balance'], 2);
                        $cell['class'] = "itemized_amount";
                    break;
                    case "negative":
                        $cell['text'] = "$".number_format($data_row['balance'], 2);
                        $cell['class'] = "itemized_amount_negative";
                    break;
                }
                if(isset($data_row['cell_override']['balance']))
                {
                    $cell['class'] = $data_row['cell_override']['balance'];
                }
            break;
            case "starting_balance":
                $cell['text'] = number_format($meta_data['starting_balance'], 2);;
                $cell['class'] = "right";
            break;
            case "ending_balance":
                $cell['text'] = number_format($meta_data['ending_balance'], 2);;
                $cell['class'] = "right";
            break;
            case "expense_account":
                $cell['text'] = $meta_data['expense_account'];
                $cell['class'] = "right";
            break;
            case "income_account":
                $cell['text'] = $meta_data['income_account'];
                $cell['class'] = "right";
            break;
            case "month_actual":
                $cell['text'] = number_format($data_row['actual_monthly'], 2);;
                $cell['class'] = "right";
            break;
            case "month_budget":
                $cell['text'] = number_format($data_row['monthly'], 2);;
                $cell['class'] = "right";
            break;
            case "month_var":
                $temp = bcsub($data_row['monthly'], $data_row['actual_monthly'], 2);
                $cell['text'] = number_format($temp, 2);;
                $cell['class'] = "right";
            break;
            case "month_pvar":
                @$cell['text'] = 100*bcdiv(($data_row['monthly']-$data_row['actual_monthly']), $data_row['monthly'], 4);
                $cell['class'] = "right";
            break;
            case "year_actual":
                $cell['text'] = number_format($data_row['actual_yearly'], 2);;
                $cell['class'] = "right";
            break;
            case "year_budget":
                $cell['text'] = number_format($date['month_count']*$data_row['monthly'], 2);
                $cell['class'] = "right";
            break;
            case "year_var":
                $temp = bcsub(($date['month_count']*$data_row['monthly']), $data_row['actual_yearly'], 2);
                $cell['text'] = number_format($temp, 2);;
                $cell['class'] = "right";
            break;
            case "year_pvar":
                @$cell['text'] = 100*bcdiv(($date['month_count']*$data_row['monthly']-$data_row['actual_yearly']), ($date['month_count']*$data_row['monthly']), 4);
                $cell['class'] = "right";
            break;
            case "annual_budget":
                @$cell['text'] = $data_row['yearly'];
                $cell['class'] = "right";
            break;
            case "as_currentowed":
                $cell['text'] = number_format($data_row['as_currentowed'], 2);
                $cell['class'] = "right_padr";
            break;
            case "as_030owed":
                $cell['text'] = number_format($data_row['as_030owed'], 2);
                $cell['class'] = "right_padr";
            break;
            case "as_3060owed":
                $cell['text'] = number_format($data_row['as_3060owed'], 2);
                $cell['class'] = "right_padr";
            break;
            case "as_6090owed":
                $cell['text'] = number_format($data_row['as_6090owed'], 2);
                $cell['class'] = "right_padr";
            break;
            case "as_o90owed":
                $cell['text'] = number_format($data_row['as_o90owed'], 2);
                $cell['class'] = "right_padr";
            break;
            case "as_prepay":
                $cell['text'] = number_format($data_row['as_prepay'], 2);
                $cell['class'] = "right_padr";
            break;
            case "as_total":
                $cell['text'] = number_format($data_row['as_total'], 2);
                $cell['class'] = "right_padr";
            break;
            case "null":
                        $cell['text'] = "&nbsp";
                        $cell['class'] = "null";
            break;
        }
        if($element_cell['style'] != 'none')
        {
            $cell['class'] = $element_cell['style'];
        }
        $row[] = $cell;
        unset($cell);
    }

    return $row;
}

public static function build_aset($arow, $account_id)
{
    $aset = array();
    switch($arow['type'])
    {
        case "group":
            $account_type = transaction::get_accounttype_by_name($arow['group']);
            $account_group = transaction::getall_top_accounts_by_type($account_type['accounttype_id']);
            //how many levels do we need to go? 
            foreach($account_group as $account)
            {
                $a_children = transaction::find_level_of_children($account['account_id']);
                if($arow['sub_level'] == "ALL")
                {
                    $aset = array_merge($aset, $a_children);
                }
                else{
                    foreach($a_children as $a_child)
                    {
                        if($a_child['level'] <= $arow['sub_level'])
                        {
                            $aset[] = $a_child;
                        }
                    }
                }
            }
            if($arow['group'] == 'EQUITY')
            {
                $aline = array("account_name"=>"CURRENT P/L", "level"=>1, "accounttype_name"=>"EQUITY");
                $aset[] = $aline;   
               
            }
        break;
        case "specific":
            if($arow['specific'] = "user_defined")
            {
                $a_children = transaction::find_level_of_children($account_id);
            }
            else{
                $a_children = transaction::find_level_of_children($arow['specific']);
            }
            if($arow['sub_level'] == "ALL")
            {
                $aset = array_merge($aset, $a_children);
            }
            else
            {
                foreach($a_children as $a_child)
                {
                    if($a_child['level'] <= $arow['sub_level'])
                    {
                        $aset[] = $a_child;
                    }
                }
            }
        break;
    }
    return $aset;
}
public static function build_dset_expense($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray)
{
    $dset = array();
    $aset = $aset_array[$drow['aset']];
    
    $target_account = $drow['target_account'];
    $target_account_string = transaction::find_subtree_string($target_account);
    $expense = 0;
    foreach($aset as $arow_index => $arow)
    {
        //get expense from aset to dset
        $expense += statement::retrieve_expense($target_account_string, $arow['account_id'], $date['start'], $date['end']);
    }
    $data_set = array("account_lines" => $dset,
                      "set_meta_data" => array("ending_balance" => $expense,
                                               "expense_account" => $accountIDtoNameArray[$target_account]) );
    return $data_set;
}
public static function build_dset_income($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray)
{
    $dset = array();
    $aset = $aset_array[$drow['aset']];
    $target_account = $drow['target_account'];
    $target_account_string = transaction::find_subtree_string($target_account);
    $income = 0;
    foreach($aset as $arow_index => $arow)
    {
        //get expense from aset to dset
        $income += statement::retrieve_income($arow['account_id'], $target_account_string, $date['start'], $date['end']);
    }
    $data_set = array("account_lines" => $dset,
                      "set_meta_data" => array("ending_balance" => $income,
                                               "income_account" => $accountIDtoNameArray[$target_account]) );
    return $data_set;
}


public static function build_dset_balance($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray)
{
    $dset = array();
    $aset = $aset_array[$drow['aset']];
    $level_total = array();
    $level_parents = array();
    foreach($aset as $arow_index => $arow)
    {
        $data_line['from_account'] = $arow['account_name'];
        $data_line['cell_override']['from_account'] = "indented_".$arow['level'];
        if($arow['account_name'] == "CURRENT P/L")
        {
            $account_type = transaction::get_accounttype_by_name("INCOME");
            //get all accounts of type income
            $income_accts = transaction::getall_top_accounts_by_type($account_type['accounttype_id']);
            $income = 0;
            // get the balance of all income to this date
            foreach($income_accts as $income_acct)
            {
                $income += transaction::account_total_date_incsub($income_acct['account_id'], "", $date['end']);
            }
            $account_type = transaction::get_accounttype_by_name("EXPENSE");
            //get all accounts of type income
            $expense_accts = transaction::getall_top_accounts_by_type($account_type['accounttype_id']);
            $expense = 0;
            // get the balance of all income to this date
            foreach($expense_accts as $expense_acct)
            {
                $expense += transaction::account_total_date_incsub($expense_acct['account_id'], "", $date['end']);
            }
            
            $current_period_total =  bcsub($income, $expense, 2);
            $data_line['balance'] = $current_period_total;
        }
        else
        {
            if($aset[$arow_index]['level'] <  @$aset[$arow_index+1]['level'])
            {
                $level_parents[$arow['level']] = $arow;
                $data_line['balance'] = transaction::account_total_date($arow['account_id'], "", $date['end'], $arow['accounttype_sign']);
                if($data_line['balance'] == 0)
                {
                    $data_line['balance'] = "";
                }
            }    
            else{
                $data_line['balance'] = transaction::account_total_date_incsub($arow['account_id'], "", $date['end']);
            }
        }
        if(!isset($level_totals[$arow['level']]))
        {
            $level_totals[$arow['level']] = 0;
        }
        $level_totals[$arow['level']] += $data_line['balance'];
    
        $data_line['sign'] = 'positive';
        if($data_line['balance'] < 0)
        {
            $data_line['sign'] = 'negative';
        }
        $dset[] = $data_line;
        unset($data_line);
        if(isset($aset[$arow_index+1]))//check if the next one exists
        {
            //if is does, is it a lower level one?
            if($aset[$arow_index]['level'] >  $aset[$arow_index+1]['level'])
            {
                $level_different =  $aset[$arow_index]['level'] - $aset[$arow_index+1]['level'];
                for($i = 0; $i < $level_different; $i++)
                {
                    $arow_parent = array_pop($level_parents);
                    $data_line['from_account'] = $arow_parent['account_name']." TOTAL";
                    $data_line['cell_override']['from_account'] = "indented_".$arow_parent['level'];
                    $data_line['balance'] = transaction::account_total_date_incsub($arow_parent['account_id'], "", $date['end']);
                    $data_line['sign'] = 'positive';
                    if($data_line['balance'] < 0)
                    {
                        $data_line['sign'] = 'negative';
                    }
                    $dset[] = $data_line;
                    unset($data_line);
                }
            }
        }
        else
        {
            while($arow_parent = array_pop($level_parents))
            {
                if($arow['accounttype_name'] == "EQUITY")
                {
                    $data_line['from_account'] = $arow_parent['account_name']." TOTAL";
                    $data_line['cell_override']['from_account'] = "indented_".$arow_parent['level'];
                    $data_line['balance'] = transaction::account_total_date_incsub($arow_parent['account_id'], "", $date['end'])+$current_period_total;
                    $data_line['sign'] = 'positive';
                    if($data_line['balance'] < 0)
                    {
                        $data_line['sign'] = 'negative';
                    }
                    $ending_balance = $data_line['balance'];
                    $dset[] = $data_line;
                    unset($data_line);
    
                }
                else{
                    $data_line['from_account'] = $arow_parent['account_name']." TOTAL";
                    $data_line['cell_override']['from_account'] = "indented_".$arow_parent['level'];
                    $data_line['balance'] = transaction::account_total_date_incsub($arow_parent['account_id'], "", $date['end']);
                    $data_line['sign'] = 'positive';
                    if($data_line['balance'] < 0)
                    {
                        $data_line['sign'] = 'negative';
                    }
                    $ending_balance = $data_line['balance'];
                    $dset[] = $data_line;
                    unset($data_line);
    
                }
            }
        }
    }
    $data_set = array("account_lines" => $dset,
                      "set_meta_data" => array("ending_balance" => $ending_balance));
    return $data_set;
}

public static function build_dset_aging($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray)
{
    $sum = array();
    $sum['from_account'] = "TOTAL";
    $sum['as_currentowed'] = 0;
    $sum['as_030owed'] = 0;
    $sum['as_3060owed'] = 0;
    $sum['as_6090owed'] = 0;
    $sum['as_o90owed'] = 0;
    $sum['as_prepay'] = 0;
    $sum['as_total'] = 0;
    $aset = $aset_array[$drow['aset']];
    foreach($aset as $arow_index => $arow)
    {
        $line = statement::aging_summary($arow['account_id'], "", $date['end'], $accountIDtoNameArray, $arow['accounttype_sign']);
        $sum['as_currentowed'] += $line['as_currentowed'];
        $sum['as_030owed'] += $line['as_030owed'];
        $sum['as_3060owed'] += $line['as_3060owed'];
        $sum['as_6090owed'] += $line['as_6090owed'];
        $sum['as_o90owed'] += $line['as_o90owed'];
        $sum['as_prepay'] += $line['as_prepay'];
        $sum['as_total'] += $line['as_total'];
        $dset[] = $line;
        unset($line);
    }
    $dset[] = $sum;
    $data_set = array("account_lines" => $dset,
                      "set_meta_data" => array());
    return $data_set;
}


public static function build_dset_reconciliation($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray, $account_subtree_query)
{
    $aset = $aset_array[$drow['aset']];
    foreach($aset as $arow_index => $arow)
    {
        $account_array[] = $arow['account_id'];
        $account_subtree_array[$arow['account_id']] = 1;
        $credit_debit = $arow['accounttype_sign']; // they should all be the same, so it should not matters
    }
    $dset = statement::bank_reconcile($account_subtree_query, $account_subtree_array, $date['start'], $date['end'], $accountIDtoNameArray, $accountIDtoFullNameArray, $credit_debit);
    $data_set = array("transaction_lines" => $dset,
                      "set_meta_data" => array());
    return $data_set;


}

public static function build_dset_nettrans($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray)
{
    //if target account specific run only one target account
    // check if include sub accounts for target accounts is set
    //or ignore it 
    $dset = array();
    $aset = $aset_array[$drow['aset']];
    $starting_balance = 0;
    foreach($aset as $arow_index => $arow)
    {
        $starting_balance += transaction::account_total_date($arow['account_id'], "", $date['pre'], $arow['accounttype_sign']);
        $account_array[] = $arow['account_id'];
        $account_subtree_array[$arow['account_id']] = 1;
        $credit_debit = $arow['accounttype_sign']; // they should all be the same, so it should not matters
    }
    $ending_balance = 0;
    foreach($aset as $arow_index => $arow)
    {
        $ending_balance += transaction::account_total_date($arow['account_id'], "", $date['end'], $arow['accounttype_sign']);
    }
    $starting_balance = bcmul($starting_balance, $drow['sign'], 2);
    $ending_balance = bcmul($ending_balance, $drow['sign'], 2);
    $dset = statement::retrieve_all_transactions_net(implode(",",$account_array), $date['start'], $date['end'], $account_subtree_array, $starting_balance, $accountIDtoNameArray, $drow['sign'], $credit_debit, $drow['data_set_sort'], $BASE_DIR);
    $data_set = array("transaction_lines" => $dset,
                      "set_meta_data" => array("starting_balance" => $starting_balance,
                                                "ending_balance" => $ending_balance));
    return($data_set);
}

public static function build_dset($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray, $account_subtree_query)
{
    $dset = array();
    switch($drow['type'])
    {
        case "balance":
            $dset = statement::build_dset_balance($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray);
        break;
        case "expense":
            $dset = statement::build_dset_expense($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray);
        break;
        case "income":
            $dset = statement::build_dset_income($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray);
        break;
        case "nettrans":
            $dset = statement::build_dset_nettrans($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray);
        break;
        case "aging":
            $dset = statement::build_dset_aging($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray);
        break;
        case "reconciliation":
            $dset = statement::build_dset_reconciliation($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray, $account_subtree_query);
        break;
        case "ledger":
            $dset = statement::build_dset_ledger($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray);
        break;
    }
    return $dset;
    
}
public static function build_statement($statement, $account_id, $dates, $mail_header, $accountIDtoNameArray, $accountIDtoFullNameArray, $BASE_DIR)
{
    $date = Statement::date_builder($dates);
    //these are needed for creating some types of elements
    $account_subtree_string = transaction::find_subtree_string($account_id);
    $account_subtree_array = transaction::find_subtree_array($account_id);
    $acount_info = transaction::get_account_byID($account_id);
    $account_name = $accountIDtoNameArray[$account_id];
    $account_fullname = $accountIDtoFullNameArray[$account_id];
    $account_subtree_query = "SELECT account_id FROM transactions_accounts WHERE account_left BETWEEN $acount_info[account_left] AND $acount_info[account_right]";
    $new_statement['name']=$statement['name'];
    $new_statement['account_memo']=$acount_info['account_memo'];
    $new_statement['name2']=$statement['name2'];
    $new_statement['header']=$statement['header'];
    $new_statement['amount_due']=transaction::account_total_date_incsub($account_id, "", $date['end']);;
    $new_statement['account_no']= $accountIDtoNameArray[$account_id];
    $new_statement['header_type']=$statement['header_type'];
    $new_statement['company_header']=$statement['company_header'];
    $new_statement['mailing_header']=$statement['mailing_header'];
    if($new_statement['mailing_header'] == 'Y')
    {
          $new_statement['mh'] = $mail_header;
    }
    $bheader = $statement['body_header'];
    $sname = $statement['name'];
    $sname2 = $statement['name2'];
    $startdate = $date['start'];
    $enddate = $date['end'];
    eval("\$bheader = \"$bheader\";");
    eval("\$sname = \"$sname\";");
    eval("\$sname2 = \"$sname2\";");
    $new_statement['name']=$sname;
    $new_statement['name2']=$sname2;
    $new_statement['body_header'] = $bheader;
    //build dataset sets
    foreach($statement['body']['arows'] as $arow_index => $arow)
    {
        $aset_array[$arow_index] = statement::build_aset($arow, $account_id);
    }
    foreach($statement['body']['drows'] as $drow_index => $drow)
    {
        $dset_array[$drow_index] = statement::build_dset($drow, $aset_array, $date, $accountIDtoNameArray, $BASE_DIR, $accountIDtoFullNameArray, $account_subtree_query);

    }
    //render table
    foreach($statement['body']['rows'] as $body_row)
    {
        switch($body_row['type'])
        {
            case "data":
                //iterate over each chosen data set
                foreach($body_row['drow_id'] as $drid)
                {
                    $data_row = $dset_array[$drid];
                    $table[] = statement::build_table_rows($data_row, $body_row['cells'], $data_row['set_meta_data']);
                }
            break;                
            case "sum_data":
                $sum_data['set_meta_data']['ending_balance']= 0 ;
                //iterate over each chosen data set
                foreach($body_row['drow_id'] as $drid)
                {
                    $data_row = $dset_array[$drid];
                    $sum_data['set_meta_data']['ending_balance'] += $data_row['set_meta_data']['ending_balance'];
                }
                $table[] = statement::build_table_rows($data_row, $body_row['cells'], $sum_data['set_meta_data']);
            break;                
            case "iterate_accounts":
                //iterate over each chosen data set
                foreach($body_row['drow_id'] as $drid)
                {
                    $data_row = $dset_array[$drid];
                    foreach($data_row['account_lines'] as $account_line)
                    {
                        $table[] = statement::build_table_rows($account_line, $body_row['cells'], $data_row['set_meta_data']);
                    }
                }
            break;                
            case "iterate_transactions":
                //iterate over each chosen data set
                foreach($body_row['drow_id'] as $drid)
                {
                    $data_row = $dset_array[$drid];
                    foreach($data_row['transaction_lines'] as $transaction_line)
                    {
                        $table[] = statement::build_table_rows($transaction_line, $body_row['cells'], $data_row['set_meta_data']);
                    }
                }
            break;                
        }
    }
    $new_statement['body'][] = $table;
    return $new_statement;
}

function retrieve_expense($withdraw, $deposit, $startdate, $enddate)
{
    $dbh = new DB_Mysql();
    if($deposit == "ALL")
    {


        $stmt = $dbh->prepare("SELECT SUM(workingtdc.transaction_dc_amount) AS expense 
                            FROM transactions_debit_credit AS workingtdc
                       INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
     LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id

                           WHERE workingtdc.transaction_account IN ($withdraw)
                             AND workingtdc.transaction_dc='DEBIT'
                            AND tm.transaction_date >= '$startdate'
                            AND tm.transaction_date <= '$enddate'");
    
    }
    else{
    $stmt = $dbh->prepare("SELECT SUM(workingtdc.transaction_dc_amount) AS expense 
                            FROM transactions_debit_credit AS workingtdc
                       INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
     LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id

                           WHERE workingtdc.transaction_account IN ($withdraw)
                             AND odc.transaction_account IN ($deposit)
                            AND tm.transaction_date >= '$startdate'
                            AND tm.transaction_date <= '$enddate'");
    }
    $stmt->execute();
    $dbRow = $stmt->fetch_assoc();
    if($dbRow['expense'] != "")
    {
        return $dbRow['expense'];
    }
    else
    {
        return "0.00";
    }
}

function retrieve_credits($account, $startdate, $enddate)
{
    $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(workingtdc.transaction_dc_amount) AS debits 
                            FROM transactions_debit_credit AS workingtdc
                       INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
                            WHERE workingtdc.transaction_account = :1:
                             AND  workingtdc.transaction_dc='CREDIT'
                            AND tm.transaction_date >= :2:
                            AND tm.transaction_date <= :3:");
    
    $stmt->execute($account, $startdate, $enddate);
    $dbRow = $stmt->fetch_assoc();
    if($dbRow['debits'] != "")
    {
        return $dbRow['debits'];
    }
    else
    {
        return "0.00";
    }
}

function retrieve_debits($account, $startdate, $enddate)
{
    $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(workingtdc.transaction_dc_amount) AS debits 
                            FROM transactions_debit_credit AS workingtdc
                       INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
                            WHERE workingtdc.transaction_account = :1:
                             AND  workingtdc.transaction_dc='DEBIT'
                            AND tm.transaction_date >= :2:
                            AND tm.transaction_date <= :3:");
    
    $stmt->execute($account, $startdate, $enddate);
    $dbRow = $stmt->fetch_assoc();
    if($dbRow['debits'] != "")
    {
        return $dbRow['debits'];
    }
    else
    {
        return "0.00";
    }
}

function retrieve_income($withdraw, $deposit, $startdate, $enddate)
{
    $dbh = new DB_Mysql();
    if($withdraw == "ALL")
    {


        $stmt = $dbh->prepare("SELECT SUM(workingtdc.transaction_dc_amount) AS income 
                            FROM transactions_debit_credit AS workingtdc
                       INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
     LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id

                           WHERE workingtdc.transaction_account IN ($deposit)
                             AND workingtdc.transaction_dc='CREDIT'
                            AND tm.transaction_date >= '$startdate'
                            AND tm.transaction_date <= '$enddate'");
    
    }
    else{
    $stmt = $dbh->prepare("SELECT SUM(workingtdc.transaction_dc_amount) AS income 
                            FROM transactions_debit_credit AS workingtdc
                       INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
     LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id

                           WHERE workingtdc.transaction_account IN ($deposit)
                             AND workingtdc.transaction_dc='CREDIT'
                           AND odc.transaction_account IN ($withdraw)
                            AND tm.transaction_date >= '$startdate'
                            AND tm.transaction_date <= '$enddate'");
    }
    $stmt->execute();
    $dbRow = $stmt->fetch_assoc();
    if($dbRow['income'] != "")
    {
        return $dbRow['income'];
    }
    else
    {
        return "0.00";
    }

}
function retrieve_net($account_string, $cat_string, $startdate, $enddate)
{
    $income = statement::retrieve_income($account_string, $cat_string, $startdate, $enddate);
    $expense = statement::retrieve_expense($account_string, $cat_string, $startdate, $enddate);
    $net = $income-$expense;
    return $net;
}
public static function retrieve_all_transactions($account_string, $startdate, $enddate)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * 
                            FROM transactions_main 
                            WHERE (transaction_accountW IN ($account_string) 
                            OR transaction_accountD IN ($account_string) )
                            AND transaction_date BETWEEN '$startdate' AND '$enddate' 
                        ORDER BY transaction_date");
    $stmt->execute();
    $dbRow = $stmt->fetchall_assoc();
    return $dbRow;
}

function sort_all_transactions($array, $sort)
{
    switch($sort)
    {
        case "prop":
            usort($array, 'compare_address');
        break;
        case "balance":
            usort($array, 'compare_balance');
        break;
        case "recurring_type":
            usort($array, 'compare_recurring_type');
        break;
        case "owners":
            usort($array, 'compare_owners');
        break;
        default:
            usort(${'hoaset'.$recurring_type['recurringtype_id']}, 'compare_owners');
    }
}
function sort_date($x, $y)
{
    if ( $x["propRecurring"]["recurringtype_name"] == $y["propRecurring"]["recurringtype_name"] )
    {
            if ( $x["fullname"] == $y["fullname"] )
            return 0;
            else if ( $x["fullname"] < $y["fullname"] )
            return -1;
            else
            return 1;
    }
    elseif ( $x["propRecurring"]["recurringtype_name"] < $y["propRecurring"]["recurringtype_name"] )
    return -1;
    else
    return 1;
}

public static function retrieve_all_transactions_net($account_string, $startdate, $enddate, $account_subtree_array, $starting, $accountIDtoNameArray, $sign, $credit_debit, $sort="date", $BASE_DIR)
{
   switch($sort)
   {
        case "account_name":
            $sort_string = " transactions_accounts.account_fullname, transaction_date";
        break;
        case "RECONCILE_DATE":
            $sort_string = " transaction_reconcile_date, transaction_checkno";
        break;
        case "CHECKNO":
            $sort_string = " transaction_checkno, transaction_date";
        break;
        case "date":
            $sort_string =  "transaction_date";
        break;
        default:
            $sort_string =  " transaction_date";
        break;

    }
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT workingtdc.transaction_dc_amount, workingtdc.transaction_dc, tm.transaction_id, tm.transaction_checkno, tm.transaction_date, tm.transaction_comment, tm.transaction_reconcile,  TF.file_id, GROUP_CONCAT(odc.transaction_account) AS split
                             FROM transactions_debit_credit AS workingtdc 
                       INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
                        LEFT JOIN transactions_debit_credit AS odc
                               ON workingtdc.transaction_id=odc.transaction_id
                        LEFT JOIN transactions_files as TF 
                               ON tm.transaction_id = TF.transaction_id
                            WHERE workingtdc.transaction_account IN ($account_string)
                              AND odc.transaction_account NOT IN ($account_string)
                              AND transaction_date BETWEEN '$startdate' AND '$enddate' 
                         GROUP BY tm.transaction_id
                         ORDER BY $sort_string");
    $stmt->execute();
    $newdbRow = array();
    $dbRow = $stmt->fetchall_assoc();
    $running_total = $starting;
    foreach($dbRow as $row)
    {
        if($credit_debit == 'DEBIT')
        {
            if($row['transaction_dc'] == 'DEBIT')
            {
                $row['credit_amount'] = "";
                $row['debit_amount'] = $row['transaction_dc_amount'];
                $running_total = bcadd($running_total, $row['transaction_dc_amount'], 2);
                $row['sign'] = "positive";
                if($sign == -1)
                {
                    $row['sign'] = "negative";
                }
            }
            if($row['transaction_dc'] == 'CREDIT')
            {
                $row['credit_amount'] = $row['transaction_dc_amount'];
                $row['debit_amount'] = "";
                $running_total = bcsub($running_total, $row['transaction_dc_amount'], 2);
                $row['sign'] = "negative";
                if($sign == -1)
                {
                    $row['sign'] = "positive";
                } 
            }
        }            
        elseif($credit_debit == 'CREDIT')
        {
            if($row['transaction_dc'] == 'DEBIT')
            {
                $row['credit_amount'] = "";
                $row['debit_amount'] = $row['transaction_dc_amount'];
                $running_total = bcsub($running_total, $row['transaction_dc_amount'], 2);
                $row['sign'] = "negative";
                if($sign == -1)
                {
                    $row['sign'] = "positive";
                }            
            }
            if($row['transaction_dc'] == 'CREDIT')
            {
                $row['credit_amount'] = $row['transaction_dc_amount'];
                $row['debit_amount'] = "";
                $running_total = bcadd($running_total, $row['transaction_dc_amount'], 2);
                $row['sign'] = "positive";
                if($sign == -1)
                {
                    $row['sign'] = "negative";
                }
            }
            
        }
        $row['running_total'] = bcmul($running_total, $sign, 2);
        if(strpos($row['split'], ','))
        {
            $row['from_account'] =  "SPLIT";
            $row['from_or_to_account'] =  "SPLIT";
        }
        else
        {
            $row['from_account'] = $accountIDtoNameArray[$row['split']];
            $row['from_or_to_account'] = $accountIDtoNameArray[$row['split']];
        }
        if($row['file_id'] != "")
        {
            $row['transaction_comment'] = "<a href=".$BASE_DIR."/interface/download_file.php?permission=transaction&file=".$row['file_id']." >".$row['transaction_comment']."</a>";
        }
        $newdbRow[] = $row;
    }
    return $newdbRow;
}
public static function retrieve_all_transactions_withdraw($account_string, $startdate, $enddate, $account_subtree_array, $starting, $accountIDtoNameArray, $sign, $credit_debit, $rec, $sort)
{
    $sql = "SELECT *
              FROM transactions_main 
        INNER JOIN transactions_accounts
                ON transactions_main.transaction_accountD=transactions_accounts.account_id
             WHERE transaction_accountW IN ($account_string) AND transaction_accountD NOT IN ($account_string) 
               AND transaction_date BETWEEN '$startdate' AND '$enddate' ";
    if($rec == "RECONCILED")
    {
        $sql .=  "AND transaction_reconcile = 'R'";
    }   
    if($rec == "UNRECONCILED")
    {
        $sql .=  "AND transaction_reconcile != 'R'";
    }   
    if($rec == "NEITHER")
    {
        $sql .=  "";
    }   
   switch($sort)
   {
        case "account":
        $sql .= " ORDER BY transactions_accounts.account_name, transaction_date";
        break;
        case "RECONCILE_DATE":
        $sql .= " ORDER BY transaction_reconcile_date, transaction_checkno";
        break;
        case "CHECKNO":
        $sql .= " ORDER BY transaction_checkno, transaction_date";
        break;
        case "DATE":
        $sql .=  " ORDER BY transaction_date";
        break;
        default:
        $sql .=  " ORDER BY transaction_date";
        break;

    }
    $running_total = $starting; 
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $running_total = $starting;
    $dbRow = $stmt->fetchall_assoc();
    return statement::process_withdraw_transactions($dbRow, $credit_debit, $accountIDtoNameArray, $sign, $starting);
}

public static function retrieve_all_transactions_debits_reconciled($account_string, $startdate, $enddate, $account_subtree_array, $starting, $accountIDtoNameArray, $sign, $credit_debit, $rec, $sort)
{
    $sql = "SELECT *, workingtdc.transaction_dc_amount AS deposits,  GROUP_CONCAT(odc.transaction_account) AS split
              FROM transactions_debit_credit AS workingtdc
         LEFT JOIN transactions_debit_credit AS odc
                ON workingtdc.transaction_id=odc.transaction_id
        INNER JOIN transactions_main AS tm
                ON tm.transaction_id=workingtdc.transaction_id 
             WHERE workingtdc.transaction_account IN ($account_string)
               AND odc.transaction_account NOT IN ($account_string) 
               AND workingtdc.transaction_dc = '$credit_debit'
               AND transaction_date <= '$enddate' 
               AND transaction_reconcile_date BETWEEN '$startdate' AND '$enddate' 
               AND transaction_reconcile = 'R'
          GROUP BY tm.transaction_id";
   switch($sort)
   {
        case "account":
        $sql .= " ORDER BY transactions_accounts.account_fullname, transaction_date";
        break;
        case "RECONCILE_DATE":
        $sql .= " ORDER BY transaction_reconcile_date, transaction_checkno";
        break;
        case "CHECKNO":
        $sql .= " ORDER BY transaction_checkno, transaction_date";
        break;
        case "DATE":
        $sql .=  " ORDER BY transaction_date";
        break;
        default:
        $sql .=  " ORDER BY transaction_date";
        break;

    }
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $dbRow = $stmt->fetchall_assoc();
    return statement::process_deposit_transactions($dbRow, $credit_debit, $accountIDtoNameArray, $sign, $starting);
}
public static function retrieve_all_transactions_debits_unreconciled($account_string, $startdate, $enddate, $account_subtree_array, $starting, $accountIDtoNameArray, $sign, $credit_debit, $rec, $sort)
{
     $sql = "SELECT *, workingtdc.transaction_dc_amount AS deposits,  GROUP_CONCAT(odc.transaction_account) AS split
              FROM transactions_debit_credit AS workingtdc
         LEFT JOIN transactions_debit_credit AS odc
                ON workingtdc.transaction_id=odc.transaction_id
        INNER JOIN transactions_main AS tm
                ON tm.transaction_id=workingtdc.transaction_id 
             WHERE workingtdc.transaction_account IN ($account_string)
               AND odc.transaction_account NOT IN ($account_string) 
               AND workingtdc.transaction_dc = '$credit_debit'
               AND (( transaction_date <= '$enddate'
                      AND transaction_reconcile != 'R')
                      OR
                        (transaction_date <= '$enddate'
                         AND transaction_reconcile = 'R'
                         AND transaction_reconcile_date > '$enddate' )) 
          GROUP BY tm.transaction_id";
   switch($sort)
   {
        case "account":
        $sql .= " ORDER BY transactions_accounts.account_fullname, transaction_date";
        break;
        case "RECONCILE_DATE":
        $sql .= " ORDER BY transaction_reconcile_date, transaction_checkno";
        break;
        case "CHECKNO":
        $sql .= " ORDER BY transaction_checkno, transaction_date";
        break;
        case "DATE":
        $sql .=  " ORDER BY transaction_date";
        break;
        default:
        $sql .=  " ORDER BY transaction_date";
        break;

    }
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $dbRow = $stmt->fetchall_assoc();
    return statement::process_deposit_transactions($dbRow, $credit_debit, $accountIDtoNameArray, $sign, $starting);
}

public static function process_withdraw_transactions($array, $credit_debit, $accountIDtoNameArray, $sign, $starting)
{
    $running_total = $starting; 
    $newdbRow = array();
    foreach($array as $row)
    {
        $row['sign'] = "negative";
        if($sign == -1)
        {
            $row['sign'] = "positive";
        }
        $running_total = bcsub($running_total, $row['transaction_amount'], 2);
        $row['running_total'] = bcmul($running_total, $sign, 2);
        $row['to_account'] = $accountIDtoNameArray[$row['split']];
        $row['from_or_to_account'] = $accountIDtoNameArray[$row['split']];
        $row['credit_amount'] = "";
        $row['debit_amount'] = $row['withdraws'];
        $newdbRow[] = $row;
    }
    return $newdbRow;
}

public static function retrieve_all_transactions_deposit($account_string, $startdate, $enddate, $account_subtree_array, $starting, $accountIDtoNameArray, $sign, $credit_debit, $rec, $sort)
{
    $sql = "SELECT *
              FROM transactions_main 
        INNER JOIN transactions_accounts
                ON transactions_main.transaction_accountW=transactions_accounts.account_id
             WHERE transaction_accountD IN ($account_string) AND transaction_accountW NOT IN ($account_string) 
               AND transaction_date BETWEEN '$startdate' AND '$enddate' ";
    if($rec == "RECONCILED")
    {
        $sql .=  "AND transaction_reconcile = 'R'";
    }   
    if($rec == "UNRECONCILED")
    {
        $sql .=  "AND transaction_reconcile != 'R'";
    }   
   switch($sort)
   {
        case "account":
        $sql .= " ORDER BY transactions_accounts.account_name, transaction_date";
        break;
        case "RECONCILE_DATE":
        $sql .= " ORDER BY transaction_reconcile_date, transaction_checkno";
        break;
        case "CHECKNO":
        $sql .= " ORDER BY transaction_checkno, transaction_date";
        break;
        case "DATE":
        $sql .=  " ORDER BY transaction_date";
        break;
        default:
        $sql .=  " ORDER BY transaction_date";
        break;

    }
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $dbRow = $stmt->fetchall_assoc();
    return statement::process_deposit_transactions($dbRow, $credit_debit, $accountIDtoNameArray, $sign, $starting);
}

public static function retrieve_all_transactions_credits_reconciled($account_string, $startdate, $enddate, $account_subtree_array, $starting, $accountIDtoNameArray, $sign, $credit_debit, $rec, $sort)
{
   $sql = "SELECT *, workingtdc.transaction_dc_amount AS withdraws,  GROUP_CONCAT(odc.transaction_account) AS split
              FROM transactions_debit_credit AS workingtdc
         LEFT JOIN transactions_debit_credit AS odc
                ON workingtdc.transaction_id=odc.transaction_id
        INNER JOIN transactions_main AS tm
                ON tm.transaction_id=workingtdc.transaction_id 
             WHERE workingtdc.transaction_account IN ($account_string)
               AND odc.transaction_account NOT IN ($account_string) 
               AND workingtdc.transaction_dc != '$credit_debit'
               AND transaction_date <= '$enddate' 
               AND transaction_reconcile_date BETWEEN '$startdate' AND '$enddate' 
               AND transaction_reconcile = 'R'
          GROUP BY tm.transaction_id";
   switch($sort)
   {
        case "account":
        $sql .= " ORDER BY transactions_accounts.account_fullname, transaction_date";
        break;
        case "RECONCILE_DATE":
        $sql .= " ORDER BY transaction_reconcile_date, transaction_checkno";
        break;
        case "CHECKNO":
        $sql .= " ORDER BY transaction_checkno, transaction_date";
        break;
        case "DATE":
        $sql .=  " ORDER BY transaction_date";
        break;
        default:
        $sql .=  " ORDER BY transaction_date";
        break;

    }
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $dbRow = $stmt->fetchall_assoc();
    return statement::process_withdraw_transactions($dbRow, $credit_debit, $accountIDtoNameArray, $sign, $starting);
}
public static function retrieve_all_transactions_credits_unreconciled($account_string, $startdate, $enddate, $account_subtree_array, $starting, $accountIDtoNameArray, $sign, $credit_debit, $rec, $sort)
{
     $sql = "SELECT *, workingtdc.transaction_dc_amount AS withdraws,  GROUP_CONCAT(odc.transaction_account) AS split
              FROM transactions_debit_credit AS workingtdc
         LEFT JOIN transactions_debit_credit AS odc
                ON workingtdc.transaction_id=odc.transaction_id
        INNER JOIN transactions_main AS tm
                ON tm.transaction_id=workingtdc.transaction_id 
             WHERE workingtdc.transaction_account IN ($account_string)
               AND odc.transaction_account NOT IN ($account_string) 
               AND workingtdc.transaction_dc != '$credit_debit'
               AND (( transaction_date <= '$enddate'
                      AND transaction_reconcile != 'R')
                      OR
                        (transaction_date <= '$enddate'
                         AND transaction_reconcile = 'R'
                         AND transaction_reconcile_date > '$enddate' )) 
          GROUP BY tm.transaction_id";
   switch($sort)
   {
        case "account":
        $sql .= " ORDER BY transactions_accounts.account_fullname, transaction_date";
        break;
        case "RECONCILE_DATE":
        $sql .= " ORDER BY transaction_reconcile_date, transaction_checkno";
        break;
        case "CHECKNO":
        $sql .= " ORDER BY transaction_checkno, transaction_date";
        break;
        case "DATE":
        $sql .=  " ORDER BY transaction_date";
        break;
        default:
        $sql .=  " ORDER BY transaction_date";
        break;

    }
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $dbRow = $stmt->fetchall_assoc();
    return statement::process_withdraw_transactions($dbRow, $credit_debit, $accountIDtoNameArray, $sign, $starting);
}

public static function process_deposit_transactions($array, $credit_debit, $accountIDtoNameArray, $sign, $starting)
{
    $row2 = array();
    $running_total = $starting; 
    foreach($array as $row)
    {
        $running_total = bcadd($running_total, $row['transaction_amount'], 2);
        $row['sign'] = "positive";
        if($sign == -1)
        {
            $row['sign'] = "negative";
        }
        $row['running_total'] = bcmul($running_total, $sign, 2);
        $row['from_account'] = $accountIDtoNameArray[$row['split']];
        $row['from_or_to_account'] = $accountIDtoNameArray[$row['split']];
        $row['credit_amount'] = $row['deposits'];
        $row['debit_amount'] = "";
        $row2[] = $row;
    }
    return $row2;
}

}
?>
