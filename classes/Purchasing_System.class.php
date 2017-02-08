<?php
/*
 * 
    This file is part of WebPropMan
    Copyright (C) 2011, Kevin Milhoan

    WebPropMan is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    WebPropMan is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

   Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

*
*/
class Purchasing_System {

    public static function create_expense_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO purchasing_accounts
                                        SET account_id=:1:,
                                            account_type='EXPENSE'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function getall_expense_accounts()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchasing_accounts INNER JOIN transactions_accounts ON transactions_accounts.account_id=purchasing_accounts.account_id WHERE account_type='EXPENSE'");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function set_default_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchasing_accounts
                                  SET default_account=0 WHERE account_type='EXPENSE'");
        $stmt->execute();
        $stmt = $dbh->prepare("UPDATE purchasing_accounts
                                  SET default_account=1
                                WHERE account_id=:1:");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function get_default_expense_account()
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchasing_accounts 
                             INNER JOIN transactions_accounts 
                                 ON transactions_accounts.account_id=purchasing_accounts.account_id 
                                 WHERE account_type='EXPENSE' and default_account=1");
        $stmt->execute();
        return $stmt->fetch_assoc();
    }
    public static function get_default_clearing_account()
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchasing_accounts 
                             INNER JOIN transactions_accounts 
                                 ON transactions_accounts.account_id=purchasing_accounts.account_id 
                                 WHERE account_type='CLEARING' and default_account=1");
        $stmt->execute();
        return $stmt->fetch_assoc();
    }
    public static function delete_expense_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM purchasing_accounts
                                WHERE account_id=:1:");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function create_disbursement_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO purchasing_accounts
                                        SET account_id=:1:,
                                            account_type='DISBURSEMENT'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function getall_disbursement_accounts()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchasing_accounts INNER JOIN transactions_accounts ON transactions_accounts.account_id=purchasing_accounts.account_id WHERE account_type='DISBURSEMENT'");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function set_default_disbursement_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchasing_accounts
                                  SET default_account=0  WHERE account_type='DISBURSEMENT'");
        $stmt->execute();
        $stmt = $dbh->prepare("UPDATE purchasing_accounts
                                  SET default_account=1
                                WHERE account_id=:1:");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function delete_disbursement_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM purchasing_accounts
                                WHERE account_id=:1:");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function create_clearing_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO purchasing_accounts
                                        SET account_id=:1:,
                                            account_type='CLEARING'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function getall_clearing_accounts()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchasing_accounts INNER JOIN transactions_accounts ON transactions_accounts.account_id=purchasing_accounts.account_id WHERE account_type='CLEARING'");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function set_default_clearing_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchasing_accounts
                                  SET default_account=0  WHERE account_type='CLEARING'");
        $stmt->execute();
        $stmt = $dbh->prepare("UPDATE purchasing_accounts
                                  SET default_account=1
                                WHERE account_id=:1: AND account_type='CLEARING'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function delete_clearing_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM purchasing_accounts
                                WHERE account_id=:1: AND account_type='CLEARING'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function create_arap_clearing_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO purchasing_accounts
                                        SET account_id=:1:,
                                            account_type='ARAP_CLEARING'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function getall_arap_clearing_accounts()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchasing_accounts INNER JOIN transactions_accounts ON transactions_accounts.account_id=purchasing_accounts.account_id WHERE account_type='ARAP_CLEARING'");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function set_default_arap_clearing_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchasing_accounts
                                  SET default_account=0  WHERE account_type='ARAP_CLEARING'");
        $stmt->execute();
        $stmt = $dbh->prepare("UPDATE purchasing_accounts
                                  SET default_account=1
                                WHERE account_id=:1: AND account_type='ARAP_CLEARING'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function delete_arap_clearing_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM purchasing_accounts
                                WHERE account_id=:1: AND account_type='ARAP_CLEARING'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }    
}



?>
