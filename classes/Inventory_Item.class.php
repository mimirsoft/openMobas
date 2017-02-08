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

class Inventory_Item{

    public static function create_inventory_item($inventory_id, $cv_id, $item_type, $native_table_id, $retail, $wholesale, $name, $description)
    {
        $dbh = new DB_Mysql();
        if($inventory_id == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO inventory_items
                                           SET inventory_id=:1:, 
                                               cv_id=:2:, 
                                               item_type=:3:, 
                                               retail_price=:4:, 
                                               wholesale_price=:5:,
                                               item_name=:6:,
                                               item_description=:7:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE inventory_items
                                           SET cv_id=:2:, 
                                               item_type=:3:, 
                                               retail_price=:4:, 
                                               wholesale_price=:5:,
                                               item_name=:6:,
                                               item_description=:7:
                                    WHERE inventory_id=:1:");
        }
        $stmt->execute($inventory_id, $cv_id, $item_type, $retail, $wholesale, $name, $description);
        return $stmt->dbh->insert_id;
    }
	
    public static function delete_inventory_item($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM inventory_items WHERE inventory_id = :1:");
        $stmt->execute($ID);
    }
    public static function getall_inventory_items_from_cvid($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, inventory_items.inventory_id, inventory_items.service_policy FROM inventory_items 
        				   INNER JOIN inventory_types 
        				           ON inventory_items.item_type = inventory_types.inventorytype_id 
        				           WHERE cv_id = :1:");
        //echo $stmt->query;
        $stmt->execute($ID);
        return  $stmt->fetchall_assoc();
    }
    public static function get_inventory_item_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, inventory_items.inventory_id
                                 FROM inventory_items 
        			       INNER JOIN user_main 
                                   ON user_main.user_id =  inventory_items.item_manager 
                           INNER JOIN cv_main ON cv_main.cv_id = inventory_items.cv_id
                            LEFT JOIN inventory_extended
        				           ON inventory_items.inventory_id = inventory_extended.inventory_id
        				        WHERE inventory_items.inventory_id = :1:");
        $stmt->execute($ID);
        return  $stmt->fetch_assoc();
    }
    public static function getall_available_items()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items WHERE available = 1");
        $stmt->execute();
        return  $stmt->fetchall_assoc();
    }
    public static function search_item_by_name($string)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items
        							INNER JOIN cv_main 
                                    ON cv_main.cv_id = inventory_items.cv_id
                                     WHERE item_name LIKE '%$string%' 
                             ORDER BY item_name");
        $stmt->execute();
        return  $stmt->fetchall_assoc();
    }
    public static function getall_current_items()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items INNER JOIN cv_main 
                                    ON cv_main.cv_id = inventory_items.cv_id 
                            WHERE inventory_items.current = 1 ORDER BY item_name");
        $stmt->execute();
        return  $stmt->fetchall_assoc();
    }
    public static function getall_current_items_from_vendor($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items WHERE current = 1 AND cv_id = :1: ORDER BY item_name");
        $stmt->execute($ID);
        return  $stmt->fetchall_assoc();
    }
    public static function getall_available_items_allowed_service()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items WHERE available = 1 AND service_policy != 'none'");
        $stmt->execute();
        return  $stmt->fetchall_assoc();
    }
    public static function getall_current_items_allowed_service()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items WHERE current = 1 AND service_policy != 'none'");
        $stmt->execute();
        return  $stmt->fetchall_assoc();
    }
    public static function getall_items_allowed_service()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items WHERE service_policy != 'none'");
        $stmt->execute();
        return  $stmt->fetchall_assoc();
    }
    public static function get_sales_and_purchase_total_dates($start, $end)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT s.sales_total,  p.purchases_total, inventory_items.inventory_id, inventory_items.item_name, inventory_items.cv_id, cv_main.cv_name
                                FROM inventory_items 
                           LEFT JOIN (SELECT SUM(invoiceitem_price_total) as sales_total, invoices_items.inventory_id
                                        FROM invoices_items
                                  INNER JOIN invoices_main
                                          ON invoices_main.invoice_id =  invoices_items.invoice_id
                                       WHERE (invoices_main.date_charged >= :1:
                                             AND invoices_main.date_charged <= :2:)
                                     GROUP BY inventory_id) AS s
                                  ON s.inventory_id = inventory_items.inventory_id
                           LEFT JOIN (SELECT SUM(purchaseitem_price_total) as purchases_total, purchase_items.inventory_id 
                                        FROM purchase_items
                                  INNER JOIN purchase_order
                                          ON purchase_order.po_id = purchase_items.po_id
                                       WHERE (purchase_order.po_date >= :1:
                                              AND purchase_order.po_date <= :2: )
                                     GROUP BY inventory_id) AS p
                                 ON inventory_items.inventory_id = p.inventory_id
                           INNER JOIN cv_main
                           ON cv_main.cv_id = inventory_items.cv_id");
        $stmt->execute($start, $end);
        //echo $stmt->query;
        return  $stmt->fetchall_assoc();
    }  
    public static function getSalesAndPurchaseTotalDatesForItem($item, $start, $end)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT s.sales_total, s.sales_count,  p.purchases_total, p.purchase_count, inventory_items.inventory_id, inventory_items.item_name, inventory_items.cv_id, cv_main.cv_name
                                FROM inventory_items 
                           LEFT JOIN (SELECT SUM(invoiceitem_price_total) as sales_total, COUNT(*) AS sales_count, invoices_items.inventory_id
                                        FROM invoices_items
                                  INNER JOIN invoices_main
                                          ON invoices_main.invoice_id =  invoices_items.invoice_id
                                       WHERE (invoices_main.date_charged >= :1:
                                             AND invoices_main.date_charged <= :2:)
                                             AND invoices_items.inventory_id=:3:
                                     GROUP BY inventory_id) AS s
                                  ON s.inventory_id = inventory_items.inventory_id
                           LEFT JOIN (SELECT SUM(purchaseitem_price_total) as purchases_total, COUNT(*) AS purchase_count, purchase_items.inventory_id 
                                        FROM purchase_items
                                  INNER JOIN purchase_order
                                          ON purchase_order.po_id = purchase_items.po_id
                                       WHERE (purchase_order.po_date >= :1:
                                              AND purchase_order.po_date <= :2: )
                                               AND purchase_items.inventory_id=:3:
                                     GROUP BY inventory_id) AS p
                                 ON inventory_items.inventory_id = p.inventory_id
                           INNER JOIN cv_main
                                    ON cv_main.cv_id = inventory_items.cv_id
                           WHERE inventory_items.inventory_id = :3:");
        $stmt->execute($start, $end, $item);
        //echo $stmt->query;
        return  $stmt->fetch_assoc();
    }  
    public static function getall_available_items_of_type($type)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items WHERE available = 1");
        $stmt->execute();
        return  $stmt->fetchall_assoc();
    }
    public static function getall_items_of_type($type)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items 
                            LEFT JOIN user_main 
                                    ON user_main.user_id = inventory_items.item_manager 
                            LEFT JOIN cv_main 
                                    ON cv_main.cv_id = inventory_items.cv_id
                                 WHERE item_type = :1: 
                              ORDER BY item_name");
        $stmt->execute($type);
        return  $stmt->fetchall_assoc();
    }
    public static function getAllItemsWithExtended($type)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items 
                            LEFT JOIN user_main 
                                    ON user_main.user_id = inventory_items.item_manager 
                            LEFT JOIN cv_main 
                                    ON cv_main.cv_id = inventory_items.cv_id
                             INNER JOIN inventory_extended
        				           ON inventory_items.inventory_id = inventory_extended.inventory_id
				               WHERE inventory_extended.$type IS NOT NULL 
                              ORDER BY item_name");
        $stmt->execute($type);
        return  $stmt->fetchall_assoc();
    }
    public static function getAllCurrentItemsWithExtended($type)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items 
                            LEFT JOIN user_main 
                                    ON user_main.user_id = inventory_items.item_manager 
                            LEFT JOIN cv_main 
                                    ON cv_main.cv_id = inventory_items.cv_id
                             INNER JOIN inventory_extended
        				           ON inventory_items.inventory_id = inventory_extended.inventory_id
				               WHERE inventory_extended.$type IS NOT NULL 
				               AND inventory_items.current = 1
                              ORDER BY item_name");
        $stmt->execute($type);
        return  $stmt->fetchall_assoc();
    }
    
    public static function getall_items()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_items INNER JOIN user_main ON user_main.user_id = inventory_items.item_manager 
                    INNER JOIN cv_main ON cv_main.cv_id = inventory_items.cv_id");
        $stmt->execute();
        return  $stmt->fetchall_assoc();
    }
    public static function set_inventory_item_prices($ID, $wholesale, $retail)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET retail_price=:3:, 
                                      wholesale_price=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $wholesale, $retail);
        return mysql_insert_id();
    }
    public static function update_description($ID, $description)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET item_description=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $description);
        return mysql_insert_id();
    }
    public static function update_availability($ID, $avail, $date)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET available=:2:,
                                      availabledate=:3:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $avail, $date);
        return mysql_insert_id();
    }
    public static function update_current($ID, $curr)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET current=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $curr);
        return mysql_insert_id();
    }
    public static function update_item_notes($ID, $notes)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET item_notes=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $notes);
        return mysql_insert_id();
    }
    public static function update_item_name($ID, $notes)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET item_name=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $notes);
        return mysql_insert_id();
    }
    public static function updateExtendedPropertyId($ID, $prop)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO inventory_extended (inventory_id, property_id) VALUES (:1:,:2:)
                    ON DUPLICATE KEY UPDATE property_id=:2:");
        $stmt->execute($ID, $prop);
        return mysql_insert_id();
    }
    
    public static function update_in_stock($ID, $stock)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET in_stock=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $stock);
        return mysql_insert_id();
    }
    public static function update_manager($ID, $manager)
    {

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET item_manager=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $manager);
        return mysql_insert_id();
    }
    public static function update_service($ID, $service)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET service_policy=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $service);
        return mysql_insert_id();
    }
    public static function update_email_on_purchase($ID, $service)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET email_vendor_on_purchase=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $service);
        return mysql_insert_id();
    }
    public static function update_on_sale_auto_purchase($ID, $service)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET on_sale_auto_purchase=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $service);
        return mysql_insert_id();
    }
    public static function update_external_link($ID, $link)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_items
                                  SET external_link=:2:
                                WHERE inventory_id=:1:");
        $stmt->execute($ID, $link);
        return mysql_insert_id();
    }

}

?>