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
*/
/*This is the transaction functions file
**
*/

class TransactionException extends Exception  {
    public $message;
    public $override;
    public $override_url;
    public $id;
    public function __construct($message, $override, $override_url, $id)
    {
        $this->message = $message;
        $this->override = $override;
        $this->override_url = $override_url;
        $this->id = $id;
    }
}


class Transaction {
    
    private $dbh;
    private $transactionAccount;
    
    public function __construct($dbh, $transactionAccount)
    {
        $this->dbh = $dbh;
        $this->transactionAccount = $transactionAccount;
        }
    
    public function add_transaction($ID, $date, $comment, $checkno, $DC_array, $override=false)
    {
        $debit = 0;
        $credit = 0;
        foreach($DC_array as $DC_line)
        {
            //verif debit credit totals
            if($DC_line['transaction_dc'] == 'DEBIT')
            {
                $debit += $DC_line['transaction_dc_amount'];
            }
            if($DC_line['transaction_dc'] == 'CREDIT')
            {
                $credit += $DC_line['transaction_dc_amount'];
            }
            //if the account for this line is locked, throw an error
            $account_info =  $this->transactionAccount->get_account_byID($DC_line['transaction_account']);
            if($account_info['account_locked'] == 1)
            {
                throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Allowed", "", "", "");
            }
            if($date <= $account_info['account_reconcile_date'])
            {
                throw new TransactionException("Date $date is less than reconciled date on account $account_info[account_name]", "", "", "");
            }
            if($account_info['account_current'] == 'N')
            {
                throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is closed.  No Transactions activity allowed", "", "", "");
            }
        }
        if($debit != $credit)
        {
            throw new TransactionException("DEBITS DO NOT EQUAL CREDITS");
        }
        else
        {
            $amount = $debit;
        }
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $this->dbh->prepare("INSERT INTO transactions_main 
                                    SET transaction_id=:1:, 
                                            transaction_date=:2:, 
                                            transaction_comment=:3:, 
                                            transaction_amount=:4:, 
                                            transaction_checkno=:5:");
            $stmt->execute($ID, $date, $comment, $amount, $checkno);
            $ID = $stmt->dbh->insert_id;
        }
        else//If it is an edit to an existing entry
        {
            $oldtrans = $this->get_transaction($ID);
            // get the debits and credits
            $old_dc = $this->get_debitcredit_by_transaction($ID);
            // check if they can be updated
            if(!$override)
            {
               foreach($old_dc as $old_dc_line)
                {
                    $account_info = $this->transactionAccount->get_account_byID($old_dc_line['transaction_account']);
                    if($oldtrans['transaction_date'] <= $account_info['account_reconcile_date'])
                    {
                        throw new TransactionException("Old transaction cannot be updated.  Transaction you are attempting to update has a date prior to the reconcile date on the account".$account_info['account_id'].", ".$account_info['account_name']);
                    }
    				if($account_info['account_locked'] == 1)
    		        {
    		            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Allowed", "", "", "");
    		        }
    		        if($account_info['account_current'] == 'N')
    		        {
    		            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is closed.  No Transactions activity allowed", "", "", "");
    		        }
                    
                }
            }
            //if they can, delete them.  
            foreach($old_dc as $old_dc_line)
            {
                $this->delete_debitcredit($old_dc_line['transaction_dc_id']);
                // then rebalance the accounts
                $this->transactionAccount->update_balance($old_dc_line['transaction_account']);
            }
            $stmt = $this->dbh->prepare("UPDATE transactions_main 
                                    SET transaction_date=:2:, 
                                    transaction_comment=:3:, 
                                    transaction_amount=:4:, 
                                    transaction_checkno=:5:
                                    WHERE transaction_id=:1:") ;
            $stmt->execute($ID, $date, $comment, $amount, $checkno);
        }
        foreach($DC_array as $DC_line)
        {
            $this->add_debitcredit('NULL', $DC_line['transaction_account'], $ID, $DC_line['transaction_dc_amount'], $DC_line['transaction_dc']);
        }
        foreach($DC_array as $DC_line)
        {
            $this->transactionAccount->update_balance($DC_line['transaction_account']);
        }
        return $ID;
    
    }

public function move_transaction($ID, $from, $to)
{
    $oldtrans = $this->get_transaction($ID);
    //if the account for this line is locked, throw an error
    $account_info =  $this->transactionAccount->get_account_byID($from);
    if($account_info['account_locked'] == 1)
    {
        throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Allowed", "", "", $ID);
    }
    if($oldtrans['transaction_date'] <= $account_info['account_reconcile_date'])
    {
        throw new TransactionException("Date $oldtrans[transaction_date] is less than reconciled date on account $account_info[account_name]", "", "", $ID);
    }
    $account_info =  $this->transactionAccount->get_account_byID($to);
    if($account_info['account_locked'] == 1)
    {
        throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Allowed", "", "", $ID);
    }
    if($oldtrans['transaction_date'] <= $account_info['account_reconcile_date'])
    {
        throw new TransactionException("Date $oldtrans[transaction_date] is less than reconciled date on account $account_info[account_name]", "", "", $ID);
    }
    
    $stmt = $this->dbh->prepare("UPDATE transactions_debit_credit 
                              SET transaction_account=:3:
                            WHERE transaction_id=:1: AND transaction_account=:2:");
    $stmt->execute($ID, $from, $to);
    $this->transactionAccount->update_balance($from);
    $this->transactionAccount->update_balance($to);
    return $ID;
}

public function reverse_transaction($ID)
{
    $oldtrans = $this->get_transaction($ID);
    // get the debits and credits
    $old_dc = $this->get_debitcredit_by_transaction($ID);
    // check if they can be updated
    foreach($old_dc as $old_dc_line)
    {
        $account_info = $this->transactionAccount->get_account_byID($old_dc_line['transaction_account']);
        if($oldtrans['transaction_date'] <= $account_info['account_reconcile_date'])
        {
            throw new TransactionException("Old transaction cannot be updated.  Transaction you are attempting to update has a date prior to the reconcile date on the account".$account_info['account_id'].", ".$account_info['account_name']);
        }
		if($account_info['account_locked'] == 1)
        {
            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Allowed", "", "", "");
        }
        if($account_info['account_current'] == 'N')
        {
            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is closed.  No Transactions activity allowed", "", "", "");
        }
    }
    //if they can, reverse them.  
    foreach($old_dc as $old_dc_line)
    {
        if($old_dc_line['transaction_dc'] == 'DEBIT')
        {
             $this->updateTdcSetCredit($old_dc_line['transaction_dc_id']);
        }
        elseif($old_dc_line['transaction_dc'] == 'CREDIT') 
        {
              $this->updateTdcSetDebit($old_dc_line['transaction_dc_id']);
            
        }
        // then rebalance the accounts
        $this->transactionAccount->update_balance($old_dc_line['transaction_account']);
    }
        
}


public function get_num_most_recent_transactions_on_account($account, $number=10)
{
    
    $stmt = $this->dbh->prepare("SELECT *
                            FROM transactions_debit_credit AS workingtdc
                       INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
                            WHERE workingtdc.transaction_account = $account
                         ORDER BY transaction_date 
                            DESC LIMIT 0, $number");
    
    $stmt->execute();
    return $stmt->fetchall_assoc();
}

public function toggle_reconcile($ID, $reconcile, $override)
{
    $oldtrans = $this->get_transaction($ID);
    if($oldtrans['transaction_reconcile_date'] == NULL)
    {
        throw new TransactionException("Transaction reconcile date is not set.");
    }
    if(!$override)
    {
        $DC_array = $this->get_debitcredit_by_transaction($ID);
        //is the date less than the reconciled date on the account?
        //is the reconcile date less than the date on the account?
        // check if they can be updated
        if($oldtrans['transaction_reconcile_date'] < $oldtrans['transaction_date'])
        {
            throw new TransactionException("Transaction cannot be marked reconciled.  Date $oldtrans[transaction_reconcile_date] is less than the date of the transaction $oldtrans[transaction_date]. Reconciled status not changed.");
        }
        foreach($DC_array as $DC_line)
        {
            $account_info =  $this->transactionAccount->get_account_byID($DC_line['transaction_account']);
            if($account_info['account_locked'] == 1)
            {
                throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Changes Allowed");
            }
            if($oldtrans['transaction_reconcile_date'] != "" && $account_info['account_reconcile_date'] != "0000-00-00" && $oldtrans['transaction_reconcile_date'] != "0000-00-00")
            {
                if($oldtrans['transaction_reconcile_date'] <= $account_info['account_reconcile_date'])
                {
                    throw new TransactionException("Date $oldtrans[transaction_reconcile_date] is less than reconciled date of $account_info[account_reconcile_date] on account $account_info[account_name]. Reconciled status not changed.");
                }
            }
        }
    }
    
    $stmt = $this->dbh->prepare("UPDATE transactions_main 
                            SET transaction_reconcile=:2:
                            WHERE transaction_id=:1:");
    $stmt->execute($ID, $reconcile);


}
public function set_reconcile_date($ID, $reconcile_date, $override)
{
    if(!$override)
    {
        $DC_array = $this->get_debitcredit_by_transaction($ID);
        //is the date less than the reconciled date on the account?
        //is the reconcile date less than the date on the account?
        $oldtrans = $this->get_transaction($ID);
        foreach($DC_array as $DC_line)
        {
            $account_info =  $this->transactionAccount->get_account_byID($DC_line['transaction_account']);
            if($account_info['account_locked'] == 1)
            {
                throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Changes Allowed");
            }
            //is the old reconciled date before the account reconciled date?
            if($oldtrans['transaction_reconcile_date'] != "" && $account_info['account_reconcile_date'] != "0000-00-00" && $oldtrans['transaction_reconcile_date'] != "0000-00-00")
            {
                if($oldtrans['transaction_reconcile_date'] <= $account_info['account_reconcile_date'])
                {
                    throw new TransactionException("Date $oldtrans[transaction_reconcile_date] is less than reconciled date of $account_info[account_reconcile_date] on account $account_info[account_name]. Reconciled status not changed.");
                }
            }
            //is the new reconciled date before the transaction date
            if($reconcile_date < $oldtrans['transaction_date'])
            {
                throw new TransactionException("Date $reconcile_date is less than the date of the transaction $oldtrans[transaction_date]. Reconciled status not changed.");
            }
            //is the new reconcile date before the account reconcile date?
            if($account_info['account_reconcile_date'] != "0000-00-00")
            {
                if($reconcile_date <= $account_info['account_reconcile_date'])
                {
                    throw new TransactionException("Date $reconcile_date is less than reconciled date of $account_info[account_reconcile_date] on account $account_info[account_name]. Reconciled status not changed.");
                }
            }
        }
    }
    
    $stmt = $this->dbh->prepare("UPDATE transactions_main 
                        SET transaction_reconcile_date=:2:
                        WHERE transaction_id=:1:");
    $stmt->execute($ID, $reconcile_date);

}

public function delete_transaction($ID, $override=false)
{
    global $BASE_DIR;
    $oldtrans = $this->get_transaction($ID);
    if($oldtrans['transaction_reconcile'] == 'R')
    {
        throw new TransactionException("Transaction cannot be deleted.  Transaction you are attempting to delete has already been reconciled", false, "", $ID);
    }
    //get the old dc
    $old_dc = $this->get_debitcredit_by_transaction($ID);
    if(!$override)
    {
        $override_url = $BASE_DIR."/transactions/ACTION=delete_transaction&transaction_id=$ID&override=true";
        foreach($old_dc as $old_dc_line)
        {
            $account_info = $this->transactionAccount->get_account_byID($old_dc_line['transaction_account']);
            if($oldtrans['transaction_date'] <= $account_info['account_reconcile_date'])
            {
                throw new TransactionException("Transaction could be deleted.  Transaction you are attempting to delete has a date prior to the reconciled date on the account ".$account_info['account_id'].", ".$account_info['account_name']." But it is not reconciled.  Think before you do this.", true, $override_url, $ID);
            }
            if($account_info['account_locked'] == 1)
            {
                throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions activity allowed, including deletions", true, $override_url, $ID);
            }
            if($account_info['account_current'] == 'N')
            {
                throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions activity allowed, including deletions", true, $override_url, $ID);
            }
        }
    }
    //delete the transaction
    
    $stmt = $this->dbh->prepare("DELETE FROM transactions_main WHERE transaction_id=:1:");
    $stmt->execute($ID);
    foreach($old_dc as $old_dc_line)
    {
    // then rebalance the accounts
        $this->transactionAccount->update_balance($old_dc_line['transaction_account']);
    }
}


public function check_all_splits()
{
    //get all transaction_debit credits were debit count > 1 or credit count > 1
    
    $stmt = $this->dbh->prepare("SELECT *, COUNT(*) 
                             FROM transactions_debit_credit
                         GROUP BY transactions_debit_credit.transaction_id
                           HAVING COUNT(*) > 2");
    $stmt->execute();
    while($row = $stmt->fetch_assoc())
    {
        $this->update_split($row['transaction_id'] , 1);
    }

}
public function update_split($ID, $split)
{
    
    $stmt = $this->dbh->prepare("UPDATE transactions_main
                                SET is_split=:2:
                            WHERE transaction_id=:1:");
    $stmt->execute($ID, $split);
    return;
}
public function update_date($ID, $date)
{
    //If it is an edit to an existing entry
    $oldtrans = $this->get_transaction($ID);
    // get the debits and credits
    $old_dc = $this->get_debitcredit_by_transaction($ID);
    // check if they can be updated
  	foreach($old_dc as $old_dc_line)
    {
        $account_info = $this->transactionAccount->get_account_byID($old_dc_line['transaction_account']);
        if($date <= $account_info['account_reconcile_date'])
        {
            throw new TransactionException("Date $date is less than reconciled date on account $account_info[account_name]", "", "", "");
        }
        if($oldtrans['transaction_date'] <= $account_info['account_reconcile_date'])
        {
            throw new TransactionException("Transaction cannot be updated.  Transaction you are attempting to update has a date prior to the reconcile date on the account".$account_info['account_id'].", ".$account_info['account_name']);
        }
		if($account_info['account_locked'] == 1)
        {
            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Allowed", "", "", "");
        }
        if($account_info['account_current'] == 'N')
        {
            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is closed.  No Transactions activity allowed", "", "", "");
        }
    }
	
    $stmt = $this->dbh->prepare("UPDATE transactions_main
                                SET transaction_date=:2:
                            WHERE transaction_id=:1:");
    $stmt->execute($ID, $date);
    return;
}
public function update_checkno($ID, $checkno)
{
    //If it is an edit to an existing entry
    $oldtrans = $this->get_transaction($ID);
    // get the debits and credits
    $old_dc = $this->get_debitcredit_by_transaction($ID);
    // check if they can be updated
    foreach($old_dc as $old_dc_line)
    {
        $account_info = $this->transactionAccount->get_account_byID($old_dc_line['transaction_account']);
        if($oldtrans['transaction_date'] <= $account_info['account_reconcile_date'])
        {
            throw new TransactionException("Transaction cannot be updated.  Transaction you are attempting to update has a date prior to the reconcile date on the account".$account_info['account_id'].", ".$account_info['account_name']);
        }
        if($account_info['account_locked'] == 1)
        {
            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Allowed", "", "", "");
        }
        if($account_info['account_current'] == 'N')
        {
            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is closed.  No Transactions activity allowed", "", "", "");
        }
    }
    
    $stmt = $this->dbh->prepare("UPDATE transactions_main
                                SET transaction_checkno=:2:
                            WHERE transaction_id=:1:");
    $stmt->execute($ID, $checkno);
    return;
}
public function update_comment($ID, $comment)
{
    //If it is an edit to an existing entry
    $oldtrans = $this->get_transaction($ID);
    // get the debits and credits
    $old_dc = $this->get_debitcredit_by_transaction($ID);
    // check if they can be updated
    foreach($old_dc as $old_dc_line)
    {
        $account_info = $this->transactionAccount->get_account_byID($old_dc_line['transaction_account']);
        if($oldtrans['transaction_date'] <= $account_info['account_reconcile_date'])
        {
            throw new TransactionException("Transaction cannot be updated.  Transaction you are attempting to update has a date prior to the reconcile date on the account".$account_info['account_id'].", ".$account_info['account_name']);
        }
        if($account_info['account_locked'] == 1)
        {
            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is LOCKED.  No Transactions Allowed", "", "", "");
        }
        if($account_info['account_current'] == 'N')
        {
            throw new TransactionException("ACCOUNT ".$account_info['account_id'].", ".$account_info['account_name']." is closed.  No Transactions activity allowed", "", "", "");
        }
    }
    
    $stmt = $this->dbh->prepare("UPDATE transactions_main
                                SET transaction_comment=:2:
                            WHERE transaction_id=:1:");
    $stmt->execute($ID, $comment);
    return;
}
public function get_transaction($ID)
{
    
    $stmt = $this->dbh->prepare("SELECT * FROM transactions_main WHERE transaction_id=:1:");
    $stmt->execute($ID);
    return $stmt->fetch_assoc();
}
public function getall_transactions_by_checkno($ID)
{
    
    $stmt = $this->dbh->prepare("SELECT * FROM transactions_main WHERE transaction_checkno=:1:");
    $stmt->execute($ID);
    return $stmt->fetchall_assoc();
}
public function getall_transactions_by_amount($ID)
{
    
    $stmt = $this->dbh->prepare("SELECT * FROM transactions_main WHERE transaction_amount=:1:");
    $stmt->execute($ID);
    return $stmt->fetchall_assoc();
}
public function getAllTransactionsOnAccount($ID)
{
    
    $stmt = $this->dbh->prepare("SELECT workingtdc.transaction_dc_amount, 
    							  workingtdc.transaction_dc, 
    							  tm.transaction_id, 
    							  tm.transaction_checkno, 
    							  tm.transaction_date, 
    							  tm.transaction_comment, 
    							  tm.transaction_reconcile, 
    							  GROUP_CONCAT(odc.transaction_account) AS split
                             FROM transactions_debit_credit AS workingtdc
                        LEFT JOIN transactions_debit_credit AS odc
                               ON workingtdc.transaction_id=odc.transaction_id AND odc.transaction_account != :1:
    				   INNER JOIN transactions_main AS tm
                               ON tm.transaction_id=workingtdc.transaction_id
                            WHERE workingtdc.transaction_account = :1:
                         GROUP BY transaction_id
                         ORDER BY tm.transaction_date, tm.transaction_id");
    $stmt->execute($ID);
    return $stmt->fetchall_assoc();
}
public function get_dc_by_transaction_account($transaction, $account)
{
    
    $stmt = $this->dbh->prepare("SELECT * FROM transactions_debit_credit WHERE transaction_id=:1: AND transaction_account=:2:");
    $stmt->execute($transaction, $account);
    return $stmt->fetch_assoc();
}





    public  function reverse_account($id)
    {
        
        //get all transaction on account
        $trans = $this->getAllTransactionsOnAccount($id);
        //for each transaction
        foreach($trans as $t_id)
        {
            //reverse the transaction
            $this->reverse_transaction($t_id['transaction_id']);
        }
    
    }

    function linkfile($ID, $file, $trans)
    {
        
        if($ID == "NULL")
        {
            $stmt = $this->dbh->prepare("INSERT INTO transactions_files 
                                           SET transaction_to_file_id=:1:, 
                                               file_id=:2:, 
                                               transaction_id=:3:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $this->dbh->prepare("UPDATE transactions_files 
                                      SET file_id=:2:, 
                                          transaction_id=:3: 
                                    WHERE transaction_to_file_id=:1:");
        }
        $stmt->execute($ID, $file, $trans);
    }

    function getall_files_of_transaction($ID)
    {
        
        $stmt = $this->dbh->prepare("SELECT * 
                                FROM transactions_files as tf, files_main as fm
                                WHERE tf.transaction_id=:1: 
                                AND tf.file_id = fm.file_id");
        $stmt->execute($ID);
        $files = $stmt->fetchall_assoc();
        return $files;
    }
    function getall_transactions_of_file($ID)
    {
        
        $stmt = $this->dbh->prepare("SELECT * 
                                FROM transactions_files as tf, transactions_main as tm
                                WHERE tf.file_id=:1: 
                                AND tf.transaction_id = tm.transaction_id");
        $stmt->execute($ID);
        $files = $stmt->fetchall_assoc();
        return $files;
    }
    
    public function add_debitcredit($ID, $account, $TID, $amount, $DebCred)
    {
        
        $stmt = $this->dbh->prepare("INSERT INTO transactions_debit_credit 
                                    SET transaction_dc_id=:1:,
                                transaction_account=:2:,
                                transaction_id=:3:,
                                transaction_dc_amount=:4:,
                                transaction_dc=:5:");
        $stmt->execute($ID, $account, $TID, $amount, $DebCred);
    }
    function updateTdcSetCredit($ID)
    {
        
        $stmt = $this->dbh->prepare("UPDATE transactions_debit_credit 
                                  SET transaction_dc='CREDIT' 
                                WHERE transaction_dc_id=:1:");
        $stmt->execute($ID);
    }
    function updateTdcSetDebit($ID)
    {
        
        $stmt = $this->dbh->prepare("UPDATE transactions_debit_credit 
                                  SET transaction_dc='DEBIT' 
                                WHERE transaction_dc_id=:1:");
        $stmt->execute($ID);
    }
    function get_debitcredit_by_transaction($TID)
    {
        
        $stmt = $this->dbh->prepare("SELECT * FROM transactions_debit_credit 
                                INNER JOIN transactions_accounts
                                       ON transactions_debit_credit.transaction_account=transactions_accounts.account_id 
                                    WHERE transaction_id=:1:");
        $stmt->execute($TID);
        return $stmt->fetchall_assoc();
    }
    function get_debits_by_transaction($TID)
    {
        
        $stmt = $this->dbh->prepare("SELECT * FROM transactions_debit_credit 
                                INNER JOIN transactions_accounts
                                       ON transactions_debit_credit.transaction_account=transactions_accounts.account_id 
                                    WHERE transaction_id=:1:
                                     AND transaction_dc='DEBIT'");
        $stmt->execute($TID);
        return $stmt->fetchall_assoc();
    }
    function get_credits_by_transaction($TID)
    {
        
        $stmt = $this->dbh->prepare("SELECT * FROM transactions_debit_credit 
                                INNER JOIN transactions_accounts
                                       ON transactions_debit_credit.transaction_account=transactions_accounts.account_id 
                                    WHERE transaction_id=:1:
                                         AND transaction_dc='CREDIT'");
        $stmt->execute($TID);
        return $stmt->fetchall_assoc();
    }
    function get_transaction_by_debitcredit($dc_id)
    {
        
        $stmt = $this->dbh->prepare("SELECT * FROM transactions_debit_credit 
                                INNER JOIN transactions_accounts
                                       ON transactions_debit_credit.transaction_account=transactions_accounts.account_id 
                                    WHERE transaction_dc_id=:1:");
        $stmt->execute($dc_id);
        return $stmt->fetch_assoc();
    }
    function delete_debitcredit($ID)
    {
        
        $stmt = $this->dbh->prepare("DELETE FROM transactions_debit_credit WHERE transaction_dc_id=:1:");
        $stmt->execute($ID);
    }

}




?>
