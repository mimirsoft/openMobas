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
class TransactionAccountException extends Exception  {
    public $message;
    public $override;
    public $override_url;
    public function __construct($message, $override, $override_url)
    {
        $this->message = $message;
        $this->override = $override;
        $this->override_url = $override_url;
    }
}
class TransactionAccount {
    private $dbh;
    private $framework;
    
    public function __construct($dbh, $framework)
    {
        $this->dbh = $dbh;
        $this->framework = $framework;
    }
    
    public function set_account_open_date($ID, $open)
    {
        
        $subtree = $this->find_subtree($ID);
        foreach($subtree as $sub)
        {
            $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                                        SET account_open_date=:2:
                                    WHERE account_id=:1:");
            $stmt->execute($sub['account_id'], $open);
        }
        return;
    }
    public function set_account_flagged($ID, $flagged)
    {
        
        $subtree = $this->find_subtree($ID);
        foreach($subtree as $sub)
        {
            $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                                        SET account_flagged=:2:
                                    WHERE account_id=:1:");
            $stmt->execute($sub['account_id'], $flagged);
        }
        return;
    }
    public function set_account_locked($ID, $locked)
    {
        
        $subtree = $this->find_subtree($ID);
        foreach($subtree as $sub)
        {
            $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                                        SET account_locked=:2:
                                    WHERE account_id=:1:");
            $stmt->execute($sub['account_id'], $locked);
        }
        //echo $stmt->query;
        return;
    }
    

    public function set_account_reconcile_date($ID, $reconcile, $override)
    {
    
        $account= $this->get_account_byID($ID);
        //get subtree
        $subtree = $this->find_subtree($ID);
        // compare reconcile dates
        if($account['account_reconcile_date'] > $reconcile && !$override)
        {
            throw new TransactionException("You are attempting to set the reconciled date on the account to a date earlier then the date already set");
        }
        else
        {
            foreach($subtree as $sub)
            {
                $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                                      SET account_reconcile_date=:2:
                                    WHERE account_id=:1:");
                $stmt->execute($sub['account_id'], $reconcile);
            }
    
        }
        return;
    }

    public function find_direct_children($parent)
    {
    
        $query = "SELECT *
                FROM transactions_accounts
          INNER JOIN transactions_accounttype
                  ON transactions_accounts.accounttype_id=transactions_accounttype.accounttype_id
               WHERE account_parent = :1:
            ORDER BY account_name ";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($parent);
        $dbRow = $stmt->fetchall_assoc();
        return $dbRow;
    }
    public function find_children($parent)
    {
    
        $stmt = $this->dbh->prepare("SELECT Parents.account_id AS PID,
                                    Children.account_id,
                                    Children.account_name,
                                    Children.account_left,
                                    Children.account_right
                            FROM transactions_accounts AS Parents,
                                    transactions_accounts AS Children
                            WHERE Children.account_left BETWEEN Parents.account_left AND Parents.account_right
                            AND Children.account_left <> Parents.account_left
                            AND Parents.account_id=:1:
                    AND NOT EXISTS
                                    (SELECT *
                                    FROM transactions_accounts AS Middle
                                    WHERE Middle.account_left BETWEEN Parents.account_left AND Parents.account_right
                                    AND Children.account_left BETWEEN Middle.account_left AND Middle.account_right
                                    AND Middle.account_id NOT IN(Parents.account_id, Children.account_id)
                                    )
                            ORDER BY account_left");
        $stmt->execute($parent);
        $dbRow = $stmt->fetchall_assoc();
        return $dbRow;
    }
    
    public function find_all_children($parent)
    {
    
        $stmt = $this->dbh->prepare("SELECT Parents.account_id AS PID,
                                    Children.*
                            FROM transactions_accounts AS Parents,
                                    transactions_accounts AS Children
                            WHERE Children.account_left BETWEEN Parents.account_left AND Parents.account_right
                            AND Children.account_left <> Parents.account_left
                            AND Parents.account_id=:1:
                            ORDER BY account_left");
        $stmt->execute($parent);
        $dbRow = $stmt->fetchall_assoc();
        return $dbRow;
    }
    public function find_level_of_children($parent)
    {
        $account_info = $this->get_account_byID($parent);
    
        $sql = "SELECT *
          FROM (SELECT A2.account_id, A2.accounttype_id, A2.account_name, A2.account_parent, A2.account_left, A2.account_right, (Count(A1.account_id))-1 AS level
                   FROM transactions_accounts AS A1,
                   transactions_accounts AS A2
                            WHERE A2.account_left BETWEEN A1.account_left AND A1.account_right
                            GROUP BY A2.account_id
                            ORDER BY A2.account_left)
            AS accounts
    INNER JOIN transactions_accounttype
            ON accounts.accounttype_id=transactions_accounttype.accounttype_id
    
         WHERE accounts.account_left
       BETWEEN ".$account_info['account_left']." AND ".$account_info['account_right'];
    
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $dbRow = $stmt->fetchall_assoc();
        return $dbRow;
    }
    
    public function find_all_parents($child)
    {
    
        $stmt = $this->dbh->prepare("SELECT Children.account_id AS CID,
                                    Parents.*
                            FROM transactions_accounts AS Parents,
                                    transactions_accounts AS Children
                            WHERE Children.account_left BETWEEN Parents.account_left AND Parents.account_right
                            AND Children.account_left <> Parents.account_left
                            AND Children.account_id=:1:
                            ORDER BY account_left");
        $stmt->execute($child);
        $dbRow = $stmt->fetchall_assoc();
        return $dbRow;
    }
    public function find_direct_parent($child)
    {
    
        $stmt = $this->dbh->prepare("SELECT T2.account_id
                            FROM transactions_accounts AS T1, transactions_accounts AS T2
                            WHERE (T1.account_left BETWEEN T2.account_left AND T2.account_right)
                            AND (T1.account_id =:1:)
                        ORDER BY T2.account_left DESC LIMIT 1,1");
        $stmt->execute($child);
        $dbRow = $stmt->fetch_assoc();
        return $dbRow;
    }
    
    public function find_subtree($parent)
    {
    
        $stmt = $this->dbh->prepare("SELECT Parents.account_id AS PID,
                                    Children.*
                            FROM transactions_accounts AS Parents,
                                    transactions_accounts AS Children
                            WHERE Children.account_left BETWEEN Parents.account_left AND Parents.account_right
                            AND Parents.account_id=:1:
                            ORDER BY account_left");
        $stmt->execute($parent);
        $dbRow = $stmt->fetchall_assoc();
        return $dbRow;
    }
    
    public function find_subtree_string($account_id)
    {
        $subtree = $this->find_subtree($account_id);
        $string = '';
        foreach($subtree as $dbRow)
        {
            $string .= $dbRow['account_id'].", ";
        }
        $string = substr($string, 0, -2);
        return $string;
    }
    
    public function find_subtree_array($account_id)
    {
        $subtree = $this->find_subtree($account_id);
        $accountTree = array();
        foreach($subtree as $dbRow)
        {
            $accountTree[$dbRow['account_id']] = true;
        }
        return $accountTree;
    }
    
    public function findSpotInTree($parent, $name)
    {
        $afterThisAccount = '';
    
        $children = $this->find_direct_children($parent);
        foreach($children as $dbRow)
        {
            if(strcasecmp($dbRow['account_name'], $name)  < 0)
            {
                $afterThisAccount = $dbRow;
            }
    
        }
        if($afterThisAccount == "")// else we must check if this is the first child account of the parent
        {
            $stmt = $this->dbh->prepare("SELECT *
                                    FROM transactions_accounts
                                    WHERE account_id = :1:");
            $stmt->execute($parent);
            $afterThisAccount = $stmt->fetch_assoc();
            $afterThisAccount['account_right'] = $afterThisAccount['account_left'];
            /*  If this is the first child of the parent, we want to spread the right from the left.
             we do this by setting the right equal to the left, effectively making it one lower
             and then running those values through the openspotintree function.
    
             */
        }
        return $afterThisAccount;
    }
    
    public function openSpotInTree($left, $right, $spread=2)
    {
    
        /*echo "UPDATE transactions_accounts
         SET account_right=account_right+$spread
         WHERE account_right > $right <BR /> ";
         */
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
            SET account_right=account_right+$spread
            WHERE account_right > :1: ");
        $stmt->execute($right);
        /*echo "UPDATE transactions_accounts
         SET account_left=account_left+$spread
         WHERE account_left > $left <BR />";
         */
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
            SET account_left=account_left+$spread
            WHERE account_left > :1: ");
        $stmt->execute($left);
    }
    
    public function closeSpotInTree($left, $right, $spread=2)
    {
    
        /*echo "UPDATE transactions_accounts
         SET account_right=account_right-$spread
         WHERE account_right > $right <BR />";
         */
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
            SET account_right=account_right-$spread
            WHERE account_right > :1: ");
        $stmt->execute($right);
        /*echo "UPDATE transactions_accounts
         SET account_left=account_left-$spread
         WHERE account_left > $left  <BR />";
         */
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
            SET account_left=account_left-$spread
            WHERE account_left > :1: ");
        $stmt->execute($left);
    }
    
    public function insertIntoTree($ID, $name, $type, $memo, $current, $left, $right)
    {
    
        $stmt = $this->dbh->prepare("INSERT INTO transactions_accounts
                                SET account_id=:1:,
                                account_name=:2:,
                                accounttype_id=:3:,
                                account_memo=:4:,
                                account_current=:5:,
                                account_left=:6:,
                                account_right=:7:");
        $stmt->execute($ID, $name, $type, $memo, $current, $left, $right);
        $ID = $stmt->dbh->insert_id;
        $this->update_fullname($ID,  $this->retrieve_account_fullname($ID));
        return $ID;
    }
    public function updateIntoTree($ID, $name, $type, $memo, $current, $left, $right)
    {
    
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                              SET account_name=:2:,
                                    accounttype_id=:3:,
                                    account_memo=:4:,
                                    account_left=:5:,
                                    account_right=:6:
                            WHERE account_id=:1: ");
        $stmt->execute($ID, $name, $type, $memo, $left, $right);
        $this->update_current($ID, $current);
        $this->update_fullname($ID,  $this->retrieve_account_fullname($ID));
    }
    
    public function add_accountnew($ID, $name, $parent, $type, $memo, $current, $override=false)
    {
        if ($name == "")
        {
            throw new Exception ("ACCOUNT name must not be empty string");
        }
        $left = '';
        $right = '';
        $afterThisAccount = '';
        if(!$override)
        {
    
            $stmt = $this->dbh->prepare("SELECT account_id,
                        account_name
                FROM transactions_accounts WHERE account_name = :1:");
            $stmt->execute($name);
            if($stmt->num_rows() > 0)
            {
                $row = $stmt->fetch_assoc();
                throw new AccountException("ACCOUNT ".$row['account_id']." already has account named ".$row['account_name'], true, "");
            }
             
        }
        //check if this is top level.  if it is not, the type must be the parent type
        if($parent != 0)
        {
            $parent_array = $this->get_account_byID($parent);
            $type = $parent_array['accounttype_id'];
    
        }
        //Find the new spot in the tree.
        $afterThisAccount = $this->findSpotInTree($parent, $name);
        $this->openSpotInTree($afterThisAccount['account_right'], $afterThisAccount['account_right']);
        $left = $afterThisAccount['account_right']+1;
        $right = $afterThisAccount['account_right']+2;
        $account_ID = $this->insertIntoTree($ID, $name, $type, $memo, $current, $left, $right);
        $this->update_parent($account_ID, $parent);
        return $account_ID;
    
    }
    
    public function update_account($ID, $name, $parent, $type, $memo, $current)
    {
        //Get the account
    
        $stmt = $this->dbh->prepare("SELECT *
                            FROM transactions_accounts
                            WHERE account_id = :1:");
        $stmt->execute($ID);
        $ThisAccount = $stmt->fetch_assoc();
        //check if this is top level.  if it is not, the type must be the parent type
        if($parent != 0)
        {
            $parent_array = $this->get_account_byID($parent);
            $type = $parent_array['accounttype_id'];
    
        }
        //Find all children
        $children = $this->find_all_children($ID);
        //Close all gaps
        $spread = $ThisAccount['account_right'] - $ThisAccount['account_left'] + 1;
        $this->closeSpotInTree($ThisAccount['account_right'], $ThisAccount['account_right'], $spread);
        //Find the new spot in the tree.
        $afterThisAccount = $this->findSpotInTree($parent, $name);
        $left = $afterThisAccount['account_right']+1;
        $right = $afterThisAccount['account_right']+$spread;
        //Open the new spot in the tree
        $this->openSpotInTree($afterThisAccount['account_right'], $afterThisAccount['account_right'], $spread);
        //Update the account.
        $this->updateIntoTree($ID, $name, $type, $memo, $current, $left, $right);
        $this->update_parent($ID, $parent);
        //Update all the children
        $shift = $left - $ThisAccount['account_left'];
        //refactor the loop below into a single update - update_children, and only update type, current, left and right
        foreach($children as $account)
        {
            if($current == 'N')
            {
                $account['account_current'] = 'N';
            }
            $this->updateIntoTree($account['account_id'],
                $account['account_name'],
                $type,
                $account['account_memo'],
                $account['account_current'],
                $account['account_left']+$shift,
                $account['account_right']+$shift);
    
        }
    }
    
    public function delete_account($ID)
    {
    
        $stmt = $this->dbh->prepare("SELECT * FROM transactions_accounts WHERE account_id=:1:");
        $stmt->execute($ID);
        $dbRow = $stmt->fetch_assoc();
        $stmt = $this->dbh->prepare("DELETE FROM transactions_accounts WHERE account_id=:1:");
        $stmt->execute($ID);
        $this->closeSpotInTree($dbRow['account_right'], $dbRow['account_right'], 1);
        $this->closeSpotInTree($dbRow['account_left'], $dbRow['account_left'], 1);
    }
    public function balance_accounts()
    {
        $stmt = $this->dbh->prepare("SELECT account_id, account_left
                            FROM transactions_accounts
                            ORDER BY account_left");
        $stmt->execute();
        while($dbRow = $stmt->fetch_assoc())
        {
            $this->set_subtotal($dbRow['account_id']);
        }
        $stmt = $this->dbh->prepare("SELECT account_id
                            FROM transactions_accounts
                            ORDER BY account_left");
        $stmt->execute();
        while($dbRow = $stmt->fetch_assoc())
        {
            $this->set_balance($dbRow['account_id']);
        }
    }
    
    
    public function update_balance($account)
    {
        $this->set_subtotal($account);
    
        $stmt = $this->dbh->prepare("SELECT Parents.account_id
                        FROM transactions_accounts AS Subacct, transactions_accounts AS Parents
                        WHERE (Subacct.account_left BETWEEN Parents.account_left AND Parents.account_right)
                        AND (Subacct.account_id =:1:)
                        ORDER BY Parents.account_left ");
        $stmt->execute($account);
        while($dbRow = $stmt->fetch_assoc())
        {
            $this->set_balance($dbRow['account_id']);
        }
    
    }
    private function set_subtotal($account)
    {
        $balance = $this->account_subtotal($account);
    
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts SET account_subtotal=:1: WHERE account_id=:2:");
        $stmt->execute($balance, $account);
    }
    
    private function set_balance($account)
    {
    
        $stmt = $this->dbh->prepare("SELECT SUM(subaccount.account_subtotal) AS balance
                            FROM transactions_accounts AS p_account, transactions_accounts AS subaccount
                            WHERE subaccount.account_left BETWEEN p_account.account_left AND p_account.account_right
                            AND p_account.account_id =:1: ");
        $stmt->execute($account);
        $dbRow = $stmt->fetch_assoc();
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                            SET account_balance=:2:
                            WHERE account_id=:1:");
        $stmt->execute($account, $dbRow['balance']);
    }
    
    
    public function get_balance($account)
    {
    
        $stmt = $this->dbh->prepare("SELECT account_balance
                        FROM transactions_accounts
                        WHERE account_id =:1: ");
        $stmt->execute($account);
        $dbRow = $stmt->fetch_assoc();
        return $dbRow['account_balance'];
    }
    
    public function add_accounttype($ID, $name, $sign)
    {
        $account_type = $this->get_accounttype_by_name($name);
        // does an account type of this name already exist?
        if($account_type['accounttype_id'] != "")
        {
            $ID = $account_type['accounttype_id'];
            // then get the id
        }
        $type = $this->get_accounttype_by_id($ID);
        if(strcasecmp($type['accounttype_name'],"ASSET")==0 ||  strcasecmp($type['accounttype_name'],"EXPENSE")==0 || strcasecmp($type['accounttype_name'],"LOSS")==0 )
        {
            $sign = 'DEBIT';
            $name = strtoupper($type['accounttype_name']);
        }
        if(strcasecmp($type['accounttype_name'], "LIABILITY")==0 || strcasecmp($type['accounttype_name'], "EQUITY")==0 ||
            strcasecmp($type['accounttype_name'],"INCOME")==0 ||  strcasecmp($type['accounttype_name'],"GAIN" )==0 )
        {
            $sign = 'CREDIT';
            $name = strtoupper($type['accounttype_name']);
        }
    
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $this->dbh->prepare("INSERT INTO transactions_accounttype
                                    SET accounttype_id = :1:,
                                            accounttype_name = :2:,
                                            accounttype_sign = :3:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $this->dbh->prepare("UPDATE transactions_accounttype
                                    SET accounttype_name=:2:,
                                    accounttype_sign=:3:
                                    WHERE accounttype_id=:1:");
        }
        $stmt->execute($ID, $name, $sign);
    
    }
    public function get_accounttype_by_id($ID)
    {
    
        $stmt = $this->dbh->prepare("SELECT * FROM transactions_accounttype WHERE accounttype_ID=:1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public function get_accounttype_by_name($ID)
    {
    
        $stmt = $this->dbh->prepare("SELECT * FROM transactions_accounttype WHERE accounttype_name=:1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public function getall_accounttypes()
    {
    
        $stmt = $this->dbh->prepare("SELECT * FROM transactions_accounttype ORDER BY accounttype_name");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    
    
    
    /* This function takes 1 argument,
     ** The selected account, for preselecting, (this is useful when editing
     */
    public function build_accountstree_selectoptions($select_array, $selected="", $skip="")
    {
        $skip_account = $this->get_account_byID($skip);
        foreach($select_array AS $row)
        {
            if(@$row['account_left'] >= $skip_account['account_left'] && @$row['account_left'] <= $skip_account['account_right'] )
            {
                continue;
            }
            echo "<OPTION value=\"".$row['account_id']."\"";
            if($row['account_id'] == $selected)
            {
                echo " SELECTED ";
            }echo ">".$row['account_fullname']."</OPTION>\n";
        }
    
    }
    
    
    public function build_accountIDtoName_array()
    {
    
        $stmt = $this->dbh->prepare("SELECT account_id,
                                    account_name
                            FROM transactions_accounts
                            ORDER BY account_left ");
        $stmt->execute();
        while($dbRow = $stmt->fetch_assoc())
        {
            $accountIDtoNameArray[$dbRow['account_id']] = $dbRow['account_name'];
        }
        return $accountIDtoNameArray;
    }
    
    public function verify_names()
    {
    
        $query = "SELECT account_id
                FROM transactions_accounts
            ORDER BY account_left ";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        while($dbRow = $stmt->fetch_assoc())
        {
            $name = $this->retrieve_account_fullname($dbRow['account_id']);
            $this->update_fullname($dbRow['account_id'],$name);
        }
        return;
    }
    public function verify_parents()
    {
    
        $query = "SELECT account_id
                FROM transactions_accounts
            ORDER BY account_left ";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        while($dbRow = $stmt->fetch_assoc())
        {
            $name = $this->find_direct_parent($dbRow['account_id']);
            $this->update_parent($dbRow['account_id'],$name['account_id']);
        }
        return;
    }
    public function verify_tree()
    {
    
        // set tree to zero
        $query = "UPDATE transactions_accounts SET account_left = 0, account_right = 0";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $account_left = 1;
        $this->walk_tree(0, $account_left);
        return;
    }
    public function walk_tree($parent, $left)
    {
    
        //get all top level accounts
        $query = "SELECT account_id
        FROM transactions_accounts
        WHERE account_parent = $parent
        ORDER BY account_name ";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        while($dbRow = $stmt->fetch_assoc())
        {
            //update tree with them
            $this->update_left($dbRow['account_id'], $left);
            $left++;
            $left = $this->walk_tree($dbRow['account_id'], $left);
            $this->update_right($dbRow['account_id'], $left);
            $left++;
        }
        return $left;
    }
    
    private function update_fullname($ID, $name)
    {
    
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                            SET account_fullname=:2:
                            WHERE account_id=:1: ");
        $stmt->execute($ID, $name);
    }
    private function update_current($ID, $current)
    {
        $account_info = $this->get_account_byID($ID);
        if($account_info['account_balance'] == 0)
        {
    
            $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                                SET account_current=:2:
                                WHERE account_id=:1: ");
            $stmt->execute($ID, $current);
        }
        elseif($account_info['account_balance'] != 0)
        {
    
            $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                                SET account_current='Y'
                                WHERE account_id=:1: ");
            $stmt->execute($ID, $current);
        }
    
    }
    
    private function update_parent($ID, $name)
    {
    
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                            SET account_parent=:2:
                            WHERE account_id=:1: ");
        $stmt->execute($ID, $name);
    }
    private function update_left($ID, $left)
    {
    
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                            SET account_left=:2:
                            WHERE account_id=:1: ");
        $stmt->execute($ID, $left );
    }
    private function update_right($ID, $right)
    {
    
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts
                            SET account_right=:2:
                            WHERE account_id=:1: ");
        $stmt->execute($ID, $right);
    }
    
    public function build_accountIDtoFullName_array($current_only)
    {
    
        $query = "SELECT account_id,
                     account_fullname,
                        account_current
            FROM transactions_accounts";
        if($current_only)
        {
            $query .= " WHERE account_current='Y' ";
        }
        $query .= " ORDER BY account_left ";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        while($dbRow = $stmt->fetch_assoc())
        {
            $accountIDtoFullnameArray[$dbRow['account_id']] = $dbRow['account_fullname'];
        }
        return $accountIDtoFullnameArray;
    }
    
    public function build_account_stack($start, $count)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
            FROM transactions_accounts
            ORDER BY account_left
            LIMIT $start, $count");
        $stmt->execute();
    
        $account_stack = $stmt->fetchall_assoc();
        return $account_stack;
    }
    public function getall_account_info($viewall)
    {
    
        $query = "SELECT *
                    FROM transactions_accounts
                       INNER JOIN transactions_accounttype
                               ON transactions_accounts.accounttype_id=transactions_accounttype.accounttype_id ";
        if($viewall != 'Y')
        {
            $query .= "WHERE account_current = 'Y' ";
        }
        $query .= "ORDER BY account_left ";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $dbRow = $stmt->fetchall_assoc();
        return $dbRow;
    }
    public function build_account_stack_all($current_only=true)
    {
    
        $query = "SELECT *
                FROM transactions_accounts";
        if($current_only)
        {
            $query .= " WHERE account_current='Y' ";
        }
        $query .= " ORDER BY account_left ASC";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
    
        $account_stack = $stmt->fetchall_assoc();
        return $account_stack;
    }
    
    public function build_account_stack_indexed($current_only=true)
    {
    
        $query = "SELECT *
                FROM transactions_accounts INNER JOIN transactions_accounttype
                               ON transactions_accounts.accounttype_id=transactions_accounttype.accounttype_id ";
        if($current_only)
        {
            $query .= " WHERE account_current='Y' ";
        }
        $query .= " ORDER BY account_left ASC";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $account_stack = $stmt->fetchall_assoc();
        foreach($account_stack as $row)
        {
            $new[$row['account_id']] = $row;
        }
        return $new;
    }
    public function get_account_byID($ID)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
                             FROM transactions_accounts
                       INNER JOIN transactions_accounttype
                               ON transactions_accounts.accounttype_id=transactions_accounttype.accounttype_id
                            WHERE transactions_accounts.account_id = :1:  ");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    
    }
    public function search_accounts($string)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
            FROM transactions_accounts
            INNER JOIN transactions_accounttype
            ON transactions_accounts.accounttype_id=transactions_accounttype.accounttype_id
            WHERE transactions_accounts.account_name LIKE '%$string%'   ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    
    }
    public function getall_accounts_by_type($ID)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
                             FROM transactions_accounts
                       INNER JOIN transactions_accounttype
                               ON transactions_accounts.accounttype_id=transactions_accounttype.accounttype_id
                            WHERE transactions_accounttype.accounttype_id = :1:
                         ORDER BY account_left ASC ");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    
    }
    public function getall_top_accounts_by_type($ID)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
                             FROM transactions_accounts
                       INNER JOIN transactions_accounttype
                               ON transactions_accounts.accounttype_id=transactions_accounttype.accounttype_id
                            WHERE transactions_accounttype.accounttype_id = :1:
                                AND account_parent = 0");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    
    }
    
    public function account_subtotal($account)
    {
    
        $stmt = $this->dbh->prepare("SELECT SUM(transaction_dc_amount) AS DEBITS
                            FROM transactions_debit_credit
                           WHERE transaction_account=:1:
                            AND transaction_dc='DEBIT'");
        $stmt->execute($account);
        $dbRow2 = $stmt->fetch_assoc();
        $DEBITS = $dbRow2['DEBITS'];
        $stmt = $this->dbh->prepare("SELECT SUM(transaction_dc_amount) AS CREDITS
                            FROM transactions_debit_credit
                            WHERE transaction_account=:1:
                              AND transaction_dc='CREDIT'");
        $stmt->execute($account);
        $dbRow2 = $stmt->fetch_assoc();
        $CREDITS = $dbRow2['CREDITS'];
        $account_info = $this->get_account_byID($account);
        if($account_info['accounttype_sign'] == 'DEBIT')
        {
            $net =  $DEBITS - $CREDITS;
        }
        else
        {
            $net =  $CREDITS - $DEBITS;
        }
        return $net;
    
    }
    //This function can calculate the total amount of an account through certain dates.
    public function account_total_date ($account, $startdate, $enddate, $sign)
    {
        if($startdate == "")
        {
            $startdate = "0000-00-00";
        }
        if($enddate == "")
        {
            $enddate = "0000-00-00";
        }
    
        $stmt = $this->dbh->prepare("SELECT SUM(transaction_dc_amount) AS expense
            FROM transactions_debit_credit AS workingtdc
            INNER JOIN transactions_main AS tm
            ON tm.transaction_id=workingtdc.transaction_id
            WHERE workingtdc.transaction_account =:1:
            AND workingtdc.transaction_dc != '$sign'
            AND tm.transaction_date >= :2:
            AND tm.transaction_date <= :3:");
        $stmt->execute($account, $startdate, $enddate);
        $dbRow2 = $stmt->fetch_assoc();
        $Expense = $dbRow2['expense'];
        $stmt = $this->dbh->prepare("SELECT SUM(transaction_amount) AS income
            FROM transactions_debit_credit AS workingtdc
            INNER JOIN transactions_main AS tm
            ON tm.transaction_id=workingtdc.transaction_id
            WHERE workingtdc.transaction_account =:1:
            AND workingtdc.transaction_dc = '$sign'
            AND tm.transaction_date >= :2:
            AND tm.transaction_date <= :3:");
        $stmt->execute($account, $startdate, $enddate);
        $dbRow2 = $stmt->fetch_assoc();
        $Income = $dbRow2['income'];
        $net = 0;
        $net = bcadd($Income, -$Expense, 2);
        return $net;
    }
    
    public function account_total_date_incsub ($account, $startdate, $enddate)
    {
    
        $stmt = $this->dbh->prepare("SELECT subaccount.account_id, accounttype_sign
                            FROM transactions_accounts AS p_account, transactions_accounts AS subaccount
                       INNER JOIN transactions_accounttype
                               ON subaccount.accounttype_id=transactions_accounttype.accounttype_id
                            WHERE subaccount.account_left BETWEEN p_account.account_left AND p_account.account_right
                            AND p_account.account_id =:1: ");
        $stmt->execute($account);
        $net = 0;
        while ($dbRow = $stmt->fetch_assoc() )
        {
            $temp = $this->account_total_date($dbRow['account_id'], $startdate, $enddate, $dbRow['accounttype_sign']);
            $net = bcadd($net, $temp, 2);
        }
        return $net;
    }
    public function account_total_date_incsub_reconciled ($account, $startdate, $enddate, $debit_credit)
    {
        $inc = $this->income_total_date_incsub_reconciled($account, $startdate, $enddate, $debit_credit);
        $exp = $this->expense_total_date_incsub_reconciled($account, $startdate, $enddate, $debit_credit);
        $net =  bcsub($inc, $exp, 2);
        return $net;
    }
    
    public function expense_total_date_incsub_reconciled($string, $startdate, $enddate, $debit_credit)
    {
    
        $stmt = $this->dbh->prepare("SELECT SUM(trans_total.expense) AS expense_total FROM (SELECT SUM(workingtdc.transaction_dc_amount) AS expense, GROUP_CONCAT(odc.transaction_account) AS split
            FROM transactions_debit_credit AS workingtdc
            LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id
            INNER JOIN transactions_main AS tm
            ON tm.transaction_id=workingtdc.transaction_id
            WHERE workingtdc.transaction_account IN ($string)
            AND odc.transaction_account NOT IN ($string)
            AND workingtdc.transaction_dc != '$debit_credit'
            AND tm.transaction_reconcile = 'R'
            AND tm.transaction_date <= :3:
            AND tm.transaction_reconcile_date >= :2:
            AND tm.transaction_reconcile_date <= :3:
            GROUP BY tm.transaction_id) AS trans_total ");
    
        $stmt->execute($string, $startdate, $enddate);
        $row = $stmt->fetch_assoc();
        $expense_sub = $row['expense_total'];
        return $expense_sub;
    }
    public function income_total_date_incsub_reconciled($string, $startdate, $enddate, $debit_credit)
    {
    
        $stmt = $this->dbh->prepare("SELECT SUM(trans_total.income) AS income_total FROM (SELECT SUM(workingtdc.transaction_dc_amount) AS income, GROUP_CONCAT(odc.transaction_account) AS split
            FROM transactions_debit_credit AS workingtdc
            LEFT JOIN transactions_debit_credit AS odc
            ON workingtdc.transaction_id=odc.transaction_id
            INNER JOIN transactions_main AS tm
            ON tm.transaction_id=workingtdc.transaction_id
            WHERE workingtdc.transaction_account IN ($string)
            AND odc.transaction_account NOT IN ($string)
            AND workingtdc.transaction_dc = '$debit_credit'
            AND tm.transaction_reconcile = 'R'
            AND tm.transaction_date <= :3:
            AND tm.transaction_reconcile_date >= :2:
            AND tm.transaction_reconcile_date <= :3:
            GROUP BY tm.transaction_id) AS trans_total");
    
        $stmt->execute($string, $startdate, $enddate);
        $row = $stmt->fetch_assoc();
        $expense_sub = $row['income_total'];
        return $expense_sub;
    }
    
    public function retrieve_account_fullname($account_id)
    {
        $string = "";
    
        $stmt = $this->dbh->prepare("SELECT Parents.account_name
                    FROM transactions_accounts AS Parents, transactions_accounts AS BaseAccount
                    WHERE (BaseAccount.account_left BETWEEN Parents.account_left AND Parents.account_right)
                    AND (BaseAccount.account_id =:1:)
                    ORDER BY Parents.account_left ");
        $stmt->execute($account_id);
        while($dbRow2 = $stmt->fetch_assoc())
        {
            $string .= $dbRow2['account_name'].":";
        }
        $string = $this->framework->XML_Replace($string);
        return $string;
    }
    
    public function retrieve_account_name($account_id)
    {
        $string = "";
    
        $stmt = $this->dbh->prepare("SELECT account_name
                    FROM transactions_accounts WHERE account_id =:1:");
        $stmt->execute($account_id);
        $dbRow2 = $stmt->fetch_assoc(); // We skip the top account
        $string .= $dbRow2['account_name'];
        $string = $this->framework->XML_Replace($string);
        return $string;
    }
    public function get_all_users_mtm_accounts($sortby, $sortby2)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
                             FROM transactions_accounts_mtm_users
                       INNER JOIN user_main
                               ON user_main.user_id = transactions_accounts_mtm_users.user_id
                       INNER JOIN transactions_accounts
                               ON transactions_accounts_mtm_users.account_id = transactions_accounts.account_id
                         ORDER BY :1:, :2:");
        $stmt->execute($sortby, $sortby2);
        $users = $stmt->fetchall_assoc();
        return $users;
    
    }
    public function create_users_mtm_accounts($id, $user, $role)
    {
    
        $stmt = $this->dbh->prepare("INSERT INTO  transactions_accounts_mtm_users
                                SET user_id=:2:,
                                    account_id=:3:,
                                    id=:1:");
        $stmt->execute($id, $user, $role);
    }
    public function getall_users_mtm_accounts_by_user_id($id)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
                             FROM transactions_accounts_mtm_users
                       WHERE user_id = :1:");
        $stmt->execute($id);
        $users = $stmt->fetchall_assoc();
        return $users;
    }
    public function getall_users_mtm_accounts_by_user_id_and_account_id($id, $id2)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
                             FROM transactions_accounts_mtm_users
                       WHERE user_id = :1:
                        AND account_id = :2:");
        $stmt->execute($id, $id2);
        $users = $stmt->fetchall_assoc();
        return $users;
    }
    
    public function get_users_mtm_accounts_by_id($id)
    {
    
        $stmt = $this->dbh->prepare("SELECT *
                             FROM transactions_accounts_mtm_users
                       WHERE id = :1:");
        $stmt->execute($id);
        $users = $stmt->fetch_assoc();
        return $users;
    
    }
    public function update_users_mtm_accounts($id, $user, $role)
    {
    
        $stmt = $this->dbh->prepare("UPDATE transactions_accounts_mtm_users
                              SET user_id=:2:,
                                  account_id=:3:
                            WHERE id=:1:");
        $stmt->execute($id, $user, $role);
    }
    
    public function delete_users_mtm_accounts($id)
    {
    
        $stmt = $this->dbh->prepare("DELETE FROM transactions_accounts_mtm_users WHERE id = :1:");
        $stmt->execute($id);
        $users = $stmt->fetch_assoc();
        return $users;
    }
}