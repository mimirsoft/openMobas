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
class Recurring_Invoice{

    public static function create_invoice($invoice_id, $cr, $last_charged, $frequency, $pretax, $tax, $total, $bname, $badd1, $badd2, $bcity, $bstate, $bzip, $sname, $sadd1, $sadd2, $scity, $sstate, $szip, $cv_id, $customer_acct, $revenue_acct)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO invoices_recurring
                                        SET recurringinvoice_id=:1:, 
                                            invoice_clientreference=:2:, 
                                            lastcharged=:3:, 
                                            frequency=:4:, 
                                            invoice_pretaxtotal=:5:, 
                                            invoice_tax=:6:, 
                                            invoice_total=:7:,
                                            billto_name=:8:,
                                            billto_address1=:9:,
                                            billto_address2=:10:,
                                            billto_city=:11:,
                                            billto_state=:12:,
                                            billto_zip=:13:,
                                            shipto_name=:14:,
                                            shipto_address1=:15:,
                                            shipto_address2=:16:,
                                            shipto_city=:17:,
                                            shipto_state=:18:,
                                            shipto_zip=:19:,
                                            customer_id=:20:,
                                            customer_account_id=:21:,
                                            revenue_account_id=:22:");
        $stmt->execute($invoice_id, $cr, $last_charged, $frequency, $pretax, $tax, $total, $bname, $badd1, $badd2, $bcity, $bstate, $bzip, $sname, $sadd1, $sadd2, $scity, $sstate, $szip, $cv_id, $customer_acct, $revenue_acct);
        return mysql_insert_id();

    }
    public static function update_invoice($invoice_id, $cr, $last_charged, $frequency, $pretax, $tax, $total, $bname, $badd1, $badd2, $bcity, $bstate, $bzip, $sname, $sadd1, $sadd2, $scity, $sstate, $szip, $cv_id, $customer_acct, $revenue_acct)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_recurring
                                    SET     invoice_clientreference=:2:, 
                                            lastcharged=:3:, 
                                            frequency=:4:, 
                                            invoice_pretaxtotal=:5:, 
                                            invoice_tax=:6:, 
                                            invoice_total=:7:,
                                            billto_name=:8:,
                                            billto_address1=:9:,
                                            billto_address2=:10:,
                                            billto_city=:11:,
                                            billto_state=:12:,
                                            billto_zip=:13:,
                                            shipto_name=:14:,
                                            shipto_address1=:15:,
                                            shipto_address2=:16:,
                                            shipto_city=:17:,
                                            shipto_state=:18:,
                                            shipto_zip=:19:,
                                            customer_id=:20:,
                                            customer_account_id=:21:,
                                            revenue_account_id=:22:
                                        WHERE recurringinvoice_id=:1:");
        $stmt->execute($invoice_id, $cr, $last_charged, $frequency, $pretax, $tax, $total, $bname, $badd1, $badd2, $bcity, $bstate, $bzip, $sname, $sadd1, $sadd2, $scity, $sstate, $szip, $cv_id, $customer_acct, $revenue_acct);
        return mysql_insert_id();

    }
    public static function delete_invoice($invoice_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM invoices_recurring
                                WHERE recurringinvoice_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();
    }
    public static function update_invoice_total($invoice_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT COALESCE(SUM(invoiceitem_price_total),0) AS total 
                                 FROM invoices_recurringitems
                                WHERE recurringinvoice_id=:1:");
        $stmt->execute($invoice_id);
        $row = $stmt->fetch_assoc();
        $stmt = $dbh->prepare("UPDATE invoices_recurring 
                                  SET invoice_total=$row[total]
                                WHERE recurringinvoice_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();

    }
    public static function update_lastcharged($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_recurring 
                                  SET lastcharged=:2:
                                WHERE recurringinvoice_id=:1:");
        $stmt->execute($invoice_id, $dc);
        return mysql_insert_id();

    }
    public static function update_start_date($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_recurring 
                                  SET start_date=:2:
                                WHERE recurringinvoice_id=:1:");
        $stmt->execute($invoice_id, $dc);
        return mysql_insert_id();

    }
    public static function update_end_date($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_recurring 
                                  SET end_date=:2:
                                WHERE recurringinvoice_id=:1:");
        $stmt->execute($invoice_id, $dc);
        return mysql_insert_id();

    }
    public static function update_description($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_recurring 
                                  SET invoice_description=:2:
                                WHERE recurringinvoice_id=:1:");
        $stmt->execute($invoice_id, $dc);
        return mysql_insert_id();

    }
    public static function add_invoiceitem($invoice_id, $cost_per, $count, $inventory_id)
    {
        $total = $cost_per*$count;
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO invoices_recurringitems
                                       SET recurringinvoiceitem_id=NULL, 
                                           recurringinvoice_id=:1:, 
                                           invoiceitem_price_per=:2:, 
                                           invoiceitem_price_total=$total, 
                                           invoiceitem_count=:3:,
                                           inventory_id=:4:");
        $stmt->execute($invoice_id, $cost_per, $count, $inventory_id);
        Recurring_Invoice::update_invoice_total($invoice_id);
        return mysql_insert_id();
    }
    public static function update_invoiceitem_count($invoiceitem_id, $count)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_recurringitems
                                  SET invoiceitem_count=:2:
                                WHERE recurringinvoiceitem_id=:1:");
        $stmt->execute($invoiceitem_id, $count);
        Recurring_Invoice::update_invoiceitem_total($invoiceitem_id);
        $invoice_item = Recurring_Invoice::get_invoiceitem($invoiceitem_id);
        Recurring_Invoice::update_invoice_total($invoice_item['recurringinvoice_id']);
        return mysql_insert_id();

    }
    public static function update_invoiceitem_price_per($invoiceitem_id, $per)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_recurringitems
                                  SET invoiceitem_price_per=:2:
                                WHERE recurringinvoiceitem_id=:1:");
        $stmt->execute($invoiceitem_id, $per);
        Recurring_Invoice::update_invoiceitem_total($invoiceitem_id);
        $invoice_item = Recurring_Invoice::get_invoiceitem($invoiceitem_id);
        Recurring_Invoice::update_invoice_total($invoice_item['recurringinvoice_id']);
        return mysql_insert_id();
    }

    public static function update_invoiceitem_total($invoiceitem_id)
    {
        $item = Recurring_Invoice::get_invoiceitem($invoiceitem_id);
        $total = $item['invoiceitem_price_per']*$item['invoiceitem_count'];
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE invoices_recurringitems
                                  SET invoiceitem_price_total=:2:
                                WHERE recurringinvoiceitem_id=:1:");
        $stmt->execute($invoiceitem_id, $total);
        return mysql_insert_id();

    }
    public static function delete_invoiceitem($invoiceitem_id)
    {
        $invoice_item = Recurring_Invoice::get_invoiceitem($invoiceitem_id);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM invoices_recurringitems
                                WHERE recurringinvoiceitem_id=:1:");
        $stmt->execute($invoiceitem_id);
        Recurring_Invoice::update_invoice_total($invoice_item['recurringinvoice_id']);
        return mysql_insert_id();
    }

    public static function getall_invoiceitems($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM invoices_recurringitems 
                           INNER JOIN inventory_items 
                                   ON inventory_items.inventory_id = invoices_recurringitems.inventory_id 
                           INNER JOIN inventory_types
                                   ON inventory_items.item_type = inventory_types.inventorytype_id 
                                WHERE recurringinvoice_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function get_invoiceitem($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM invoices_recurringitems 
                           INNER JOIN inventory_items 
                                   ON inventory_items.inventory_id = invoices_recurringitems.inventory_id 
                           INNER JOIN inventory_types
                                   ON inventory_items.item_type = inventory_types.inventorytype_id 
                                WHERE invoices_recurringitems.recurringinvoiceitem_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }

    public static function get_invoice($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, ra.account_fullname AS revenueaccount_fullname,  ca.account_fullname as customeraccount_fullname
                                 FROM invoices_recurring 
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = invoices_recurring.customer_id 
                           INNER JOIN transactions_accounts AS ca
                                   ON ca.account_id = invoices_recurring.customer_account_id 
                           INNER JOIN transactions_accounts AS ra
                                   ON ra.account_id = invoices_recurring.revenue_account_id 
                                WHERE recurringinvoice_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function getall_invoices()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM invoices_recurring INNER JOIN cv_main ON cv_main.cv_id = invoices_recurring.customer_id");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_invoices_of_customer($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM invoices_recurring INNER JOIN cv_main ON cv_main.cv_id = invoices_recurring.customer_id
                                WHERE customer_id=:1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_openinvoices()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM invoices_recurring INNER JOIN cv_main ON cv_main.cv_id = invoices_recurring.customer_id  WHERE invoice_charged IS NULL");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_unpaidinvoices()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM invoices_recurring INNER JOIN cv_main ON cv_main.cv_id = invoices_recurring.customer_id 
                                WHERE invoice_charged IS NOT NULL AND invoice_paid IS NULL");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function charge_recurring($recurring_id, $date)
    {
        //get the recurring invoice
        $ri = Recurring_Invoice::get_invoice($recurring_id);
        //create a normal invoice from it
        $invoice_id = Invoice::create_invoice(NULL, $ri['invoice_clientreference'], 0.00, 0.00, 0.00,
                            $ri['billto_name'], $ri['billto_address1'], $ri['billto_address2'], $ri['billto_city'], $ri['billto_state'], $ri['billto_zip'],
                            $ri['shipto_name'], $ri['shipto_address1'], $ri['shipto_address2'], $ri['shipto_city'], $ri['shipto_state'], $ri['shipto_zip'],
                            $ri['customer_id'], $ri['customer_account_id'], $ri['revenue_account_id']);
        Invoice::update_description($invoice_id, $ri['invoice_description']);
        $rinvoice_items = Recurring_Invoice::getall_invoiceitems($recurring_id);
        foreach($rinvoice_items as $r_item)
        {
            //add the item
            Invoice::add_invoiceitem($invoice_id, $r_item['invoiceitem_price_per'],  $r_item['invoiceitem_count'], $r_item['inventory_id']);
        }
        //charge that normal invoice
        Invoice::charge_invoice($invoice_id, $date);
        //update last charged on the recurring
        Recurring_Invoice::update_lastcharged($recurring_id, $date);
    }

}

?>