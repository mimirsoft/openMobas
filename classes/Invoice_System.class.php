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
class Invoice_System {

    public static function create_revenue_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO invoices_accounts
                                        SET account_id=:1:,
                                            account_type='REVENUE'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function getall_revenue_accounts()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM invoices_accounts INNER JOIN transactions_accounts ON transactions_accounts.account_id=invoices_accounts.account_id WHERE account_type='REVENUE'");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function set_default_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_accounts
                                  SET default_account=0 WHERE account_type='REVENUE'");
        $stmt->execute();
        $stmt = $dbh->prepare("UPDATE invoices_accounts
                                  SET default_account=1
                                WHERE account_id=:1:");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function delete_revenue_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM invoices_accounts
                                WHERE account_id=:1:");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function create_remittance_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO invoices_accounts
                                        SET account_id=:1:,
                                            account_type='REMITTANCE'");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function getall_remittance_accounts()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM invoices_accounts INNER JOIN transactions_accounts ON transactions_accounts.account_id=invoices_accounts.account_id WHERE account_type='REMITTANCE'");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function set_default_remittance_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_accounts
                                  SET default_account=0  WHERE account_type='REMITTANCE'");
        $stmt->execute();
        $stmt = $dbh->prepare("UPDATE invoices_accounts
                                  SET default_account=1
                                WHERE account_id=:1:");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }
    public static function delete_remittance_account($account_id)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM invoices_accounts
                                WHERE account_id=:1:");
        $stmt->execute($account_id);
        return mysql_insert_id();
    }

}



?>
