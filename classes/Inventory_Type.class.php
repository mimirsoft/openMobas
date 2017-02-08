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

class Inventory_Type{

    public static function create_inventory_type($ID, $name, $policy, $generate_po, $has_native, $native_table_name)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO inventory_types
                                        SET inventorytype_id=:1:, 
                                            inventorytype_name=:2:, 
                                            service_policy=:3:, 
                                            onsale_generate_po=:4:,
                                            nativetable_name=:5:");
        $stmt->execute($ID, $name, $policy, $generate_po, $native_table_name);
        return mysql_insert_id();
        

    }
    public static function update_inventory_type($ID, $name, $policy, $generate_po, $has_native, $native_table_name)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE inventory_types
                                        SET inventorytype_name=:2:, 
                                            service_policy=:3:, 
                                            onsale_generate_po=:4:,
                                            nativetable_name=:5:
                                       WHERE inventorytype_id=:1:");
        $stmt->execute($ID, $name, $policy, $generate_po, $native_table_name);
        return mysql_insert_id();


    }

    public static function get_inventory_type($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_types WHERE inventorytype_id = :1:");
        $stmt->execute($ID);
        return  $stmt->fetch_assoc();

    }
    public static function getInventoryTypeByName($name)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_types WHERE inventorytype_name = :1:");
        $stmt->execute($name);
        return  $stmt->fetch_assoc();

    }
    public static function getall_inventory_types()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM inventory_types ORDER by inventorytype_name");
        $stmt->execute();
        return  $stmt->fetchall_assoc();

    }
}

?>