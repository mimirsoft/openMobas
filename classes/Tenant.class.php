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

class Tenant{

function create($cv_id, $inventory, $start, $end)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("INSERT INTO tenants_main 
                                   SET cv_id=:1:, 
                                       inventory_id=:2:, 
                                       lease_start_date=:3:, 
                                       lease_end_date=:4:");
    $stmt->execute($cv_id, $inventory, $start, $end);
    return mysql_insert_id();
    
}
function updateRent($ID, $rent)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("UPDATE tenants_main 
                              SET tenant_rent=:2:
                            WHERE tenant_id=:1:");
    $stmt->execute($ID, $rent);
}
function update30Day($ID, $date)
{
    if($date != '')
    {
        //set the move out date to 30 days later
        $moveout = strtotime("+30 day", strtotime($date));
        $moveout_date =  date("Ymd", $moveout);
        //set the refund date to 21 days after that
        $refund = strtotime("+21 day", $moveout);
        $refund_date =  date("Ymd", $refund);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE tenants_main 
                                  SET thirty_day_date=:2:,
                                  move_out_date=:3:,
                                  refund_date=:4:
                                  WHERE tenant_id=:1:");
        $stmt->execute($ID, $date, $moveout_date, $refund_date);
    }
    else
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE tenants_main 
                                  SET thirty_day_date=NULL,
                                  move_out_date=NULL,
                                  refund_date=NULL
                                  WHERE tenant_id=:1:");
        $stmt->execute($ID);
    }
}
function update($ID, $current, $property, $start, $end)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("UPDATE tenants_main 
                              SET tenant_current=:2:,
                                  inventory_id=:3:, 
                                  lease_start_date=:4:,
                                  lease_end_date=:5: 
                            WHERE tenant_id=:1:");
    $stmt->execute($ID, $current, $property, $start, $end);
}

function delete($ID)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("DELETE FROM tenants_main WHERE tenant_id=:1:");
    $stmt->execute($ID);
}
function getTenantByID($ID)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT *, inventory_items.inventory_id 
                             FROM tenants_main 
                             INNER JOIN inventory_items ON inventory_items.inventory_id=tenants_main.inventory_id
                             INNER JOIN cv_main ON cv_main.cv_id = tenants_main.cv_id
                           LEFT JOIN inventory_extended
        				           ON inventory_items.inventory_id = inventory_extended.inventory_id
				            WHERE tenant_id=:1:");
    $stmt->execute($ID);
    return $stmt->fetch_assoc();

}
function getall_tenants()
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * 
                             FROM tenants_main 
                             INNER JOIN cv_main ON cv_main.cv_id=tenants_main.cv_id
                            INNER JOIN properties_main ON properties_main.property_id=tenants_main.property_id");
    $stmt->execute();
    return $stmt->fetchall_assoc();

}
function getAllTenantsByStatus($status, $sort='cv_name', $sort2='lease_end_date')
{
    //you may want to add acs/desc variables that can be passed too
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT *, tenants_main.cv_id 
                             FROM tenants_main 
                             INNER JOIN cv_main ON cv_main.cv_id=tenants_main.cv_id
                             INNER JOIN inventory_items ON inventory_items.inventory_id=tenants_main.inventory_id
                             WHERE tenant_current=:1:
                             ORDER BY ".$sort." DESC, ".$sort2." ASC");
    $stmt->execute($status);
    return $stmt->fetchall_assoc();

}
//these below have not been updated

function add_tenants_multi($multi_ID, $main_ID, $contact_ID)
{
    $dbh = new DB_Mysql();
    if($multi_ID == "NULL")// If it is a new entry.
    {
        $stmt = $dbh->prepare("INSERT INTO tenants_multi 
                                       SET tenantmulti_id=:1:, 
                                           tenant_id=:2:, 
                                           contacts_id=:3:");
    }
    else//If it is an edit to an existing entry
    {
        $stmt = $dbh->prepare("UPDATE tenants_multi 
                                  SET tenant_id=:2:, 
                                      contacts_id=:3: 
                                WHERE tenantmulti_id=:1:");
    }
    $stmt->execute($multi_ID, $main_ID, $contact_ID);
}
function delete_tenants_multi($multi_ID)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("DELETE FROM tenants_multi WHERE tenantmulti_id=:1:");
    $stmt->execute($multi_ID);
}

function add_link($ID, $tenant, $account)
{
    $dbh = new DB_Mysql();
    if($ID == "NULL")// If it is a new entry.
    {
        $stmt = $dbh->prepare("INSERT INTO tenants_accounts 
                                       SET tenantaccount_id=:1:, 
                                           tenant_id=:2:, 
                                           account_id=:3:");
    }
    else//If it is an edit to an existing entry
    {
        $stmt = $dbh->prepare("UPDATE tenants_accounts 
                                  SET tenant_id=:2:, 
                                      account_id=:3: 
                                WHERE tenantaccount_id=:1:");
    }
    $stmt->execute($ID, $tenant, $account);
}

function delete_link($ID)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("DELETE FROM tenants_accounts WHERE tenantaccount_id=:1:");
    $stmt->execute($ID);
}
function getall_tenants_by_property_status($prop, $status)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * 
                             FROM tenants_main 
                            WHERE property_id='$prop' AND tenant_current=:1:");
    $stmt->execute($status);
    return $stmt->fetchall_assoc();

}


function getall_tenants_multi($tenant)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT *, CONCAT(lastname,', ',firstname) AS full_name 
                                  
                             FROM tenants_multi 
                        LEFT JOIN contacts_main
                               ON contacts_main.contacts_id = tenants_multi.contacts_id
                            WHERE tenant_id=:1:");
    $stmt->execute($tenant);
    return $stmt->fetchall_assoc();

}         

}
?>
