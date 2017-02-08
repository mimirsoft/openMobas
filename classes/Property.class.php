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
class Property{

    // This is the function to add or update a property
    
    public static function add_property($ID, $address, $aptnum, $propnum, $city, $state, $county, $zip, $thomasguidenum,
        $complex, $area, $garagenum, $parknum, $mailboxnum, $sqft, $yrblt, $numrm, $numbed, $numbath, $refridge, $dishwasher, 
        $stove, $microwave, $fireplace, $air, $heat, $garage, $openers, $poolspa, $landscaping, 
        $pets, $petdeposit, $smoking, $whopaygas, $whopayelec, $whopaywater, $whopaygarbage, $rentdesired, $rentmin, 
        $leasedesired, $leasemin, $dateavail, $properties_type_ID, $feepercent, $property_current, $property_avail, 
        $property_sale, $property_shortterm, $property_comments, $description, $petdescription, $garageinfo, $maintenance, $washer, $dryer, $parking )
    {
        $sqft = ereg_replace(",","",$sqft);
        $address = mysql_real_escape_string($address);
        $dbh = new DB_Mysql();
        if($ID == "NULL")
        {
            $stmt = $dbh->prepare("INSERT INTO properties_main 
                                           SET property_id='$ID', 
                                               property_address='$address', 
                                               property_aptnum='$aptnum', 
                                               property_propnum='$propnum', 
                                               property_city='$city', 
                                               property_state='$state', 
                                               property_zip='$zip', 
                                               property_county='$county', 
                                               property_thomasguidenum='$thomasguidenum', 
                                               property_complex=:3:, 
                                               property_area='$area', 
                                               property_garagenum='$garagenum', 
                                               property_garageinfo='$garageinfo', 
                                               property_parking='$parking', 
                                               property_parknum='$parknum', 
                                               property_mailboxnum='$mailboxnum', 
                                               property_sqft='$sqft', 
                                               property_yrblt='$yrblt', 
                                               property_numrm='$numrm', 
                                               property_numbed='$numbed', 
                                               property_numbath='$numbath', 
                                               property_refridge='$refridge', 
                                               property_dishwasher='$dishwasher', 
                                               property_stove='$stove', 
                                               property_washer='$washer', 
                                               property_dryer='$dryer', 
                                               property_microwave='$microwave', 
                                               property_fireplace='$fireplace', 
                                               property_air='$air', 
                                               property_heat='$heat', 
                                               property_garage='$garage', 
                                               property_openers='$openers', 
                                               property_pool='$poolspa', 
                                               property_landscaping='$landscaping', 
                                               property_pets='$pets', 
                                               property_petdeposit='$petdeposit', 
                                               property_petdescription='$petdescription', 
                                               property_smoking='$smoking', 
                                               property_whopaygas='$whopaygas', 
                                               property_whopayelec='$whopayelec', 
                                               property_whopaywater='$whopaywater', 
                                               property_whopaygarbage='$whopaygarbage', 
                                               property_rentdesired='$rentdesired', 
                                               property_rentmin='$rentmin', 
                                               property_leasedesired='$leasedesired', 
                                               property_leasemin='$leasemin', 
                                               property_dateavail='$dateavail', 
                                               propertytype_id='$properties_type_ID', 
                                               property_feepercent='$feepercent', 
                                               property_status='$property_current',
                                               property_avail='$property_avail',
                                               property_sale='$property_sale',
                                               property_shortterm='$property_shortterm',
                                               property_maintenance='$maintenance',
                                               property_comments=:1:,
                                               property_description=:2:");
        }
        else//if($ID != "NULL")
        {
            $stmt = $dbh->prepare("UPDATE properties_main
                                        SET property_address='$address', 
                                            property_aptnum='$aptnum', 
                                            property_propnum='$propnum', 
                                            property_city='$city', 
                                            property_state='$state', 
                                            property_zip='$zip', 
                                            property_county='$county', 
                                            property_thomasguidenum='$thomasguidenum', 
                                            property_complex=:3:, 
                                            property_area='$area', 
                                            property_garagenum='$garagenum', 
                                            property_garageinfo='$garageinfo', 
                                            property_parknum='$parknum', 
                                            property_parking='$parking', 
                                            property_mailboxnum='$mailboxnum', 
                                            property_sqft='$sqft', 
                                            property_yrblt='$yrblt', 
                                            property_numrm='$numrm', 
                                            property_numbed='$numbed', 
                                            property_numbath='$numbath', 
                                            property_refridge='$refridge', 
                                            property_dishwasher='$dishwasher', 
                                            property_stove='$stove', 
                                            property_washer='$washer', 
                                            property_dryer='$dryer', 
                                            property_microwave='$microwave', 
                                            property_fireplace='$fireplace', 
                                            property_air='$air', 
                                            property_heat='$heat', 
                                            property_garage='$garage', 
                                            property_openers='$openers', 
                                            property_pool='$poolspa', 
                                            property_landscaping='$landscaping', 
                                            property_pets='$pets', 
                                            property_petdeposit='$petdeposit', 
                                            property_petdescription='$petdescription', 
                                            property_smoking='$smoking', 
                                            property_whopaygas='$whopaygas', 
                                            property_whopayelec='$whopayelec', 
                                            property_whopaywater='$whopaywater', 
                                            property_whopaygarbage='$whopaygarbage', 
                                            property_rentdesired='$rentdesired', 
                                            property_rentmin='$rentmin', 
                                            property_leasedesired='$leasedesired', 
                                            property_leasemin='$leasemin', 
                                            property_dateavail='$dateavail', 
                                            propertytype_id='$properties_type_ID', 
                                            property_feepercent='$feepercent', 
                                            property_status='$property_current', 
                                            property_avail='$property_avail',
                                            property_sale='$property_sale',
                                            property_shortterm='$property_shortterm',
                                            property_maintenance='$maintenance',
                                            property_comments=:1:,
                                            property_description=:2:
                                      WHERE property_id='$ID'");
        }
        $stmt->execute($property_comments, $description, $complex);
        $propID = mysql_insert_id();
        return $propID;
    }
    
    function get_property($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM properties_main 
                                INNER JOIN properties_type
                                ON properties_main.propertytype_id = properties_type.propertytype_id
                                WHERE property_id=:1:");
        $stmt->execute($ID);
        $prop = $stmt->fetch_assoc();
        return $prop;
    }
    function select_properties($limited, $status)
    {
        $dbh = new DB_Mysql();
        if($limited)
        {
            $stmt = $dbh->prepare("SELECT * 
                                     FROM properties_main
                                    WHERE property_status = :1: 
                                 ORDER BY property_address, property_aptnum");
            $stmt->execute($status);
            $properties = $stmt->fetchall_assoc();
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("SELECT * 
                                     FROM properties_main
                                 ORDER BY property_address, property_aptnum");
            $stmt->execute();
            $properties = $stmt->fetchall_assoc();
        }
        return $properties;
    }

    function add_property_type($ID, $name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO properties_type 
                                           SET propertytype_id='$ID', 
                                               propertytype_name='$name'");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE properties_type 
                                      SET propertytype_name='$name' 
                                    WHERE propertytype_id='$ID'");
        }
       $stmt->execute();
    }
    
    function add_link($ID, $property, $account, $type)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")
        {
            $stmt = $dbh->prepare("INSERT INTO properties_accounts 
                                           SET propertyaccount_id=:1:, 
                                               property_id=:2:, 
                                               account_id=:3:,
                                               account_type=:4:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE properties_accounts 
                                      SET property_id=:2:, 
                                          account_id=:3:,
                                          account_type=:4:
                                   WHERE propertyaccount_id=:1:");
        }
        $stmt->execute($ID, $property, $account, $type);
    }
    function add_owner($ID, $prop, $owner, $percent)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")
        {
            $stmt = $dbh->prepare("INSERT INTO property_owners 
                                           SET property_owner_id=:1:, 
                                               property_id=:2:, 
                                               contacts_id=:3:, 
                                               ownership_percentage=:4:");
        }
        $stmt->execute($ID, $prop, $owner, $percent);
    }
    function delete_owner($ID)
    {
        $dbh = new DB_Mysql();
    
        $stmt = $dbh->prepare("DELETE FROM property_owners 
                                   WHERE property_owner_id=:1:");
        $stmt->execute($ID);
    }

    function update_percentage($ID, $percent)
    {
        $dbh = new DB_Mysql();
            $stmt = $dbh->prepare("UPDATE property_owners 
                                      SET ownership_percentage=:2:
                                     WHERE property_owner_id=:1:");
        $stmt->execute($ID, $percent);
    }
    function set_availability($ID, $avail)
    {
        $dbh = new DB_Mysql();
            $stmt = $dbh->prepare("UPDATE properties_main 
                                      SET property_avail=:2:
                                     WHERE property_id=:1:");
        $stmt->execute($ID, $avail);
    }
    function updateHOA($ID, $avail)
    {
        $dbh = new DB_Mysql();
            $stmt = $dbh->prepare("UPDATE properties_main 
                                      SET property_hoa=:2:
                                     WHERE property_id=:1:");
        $stmt->execute($ID, $avail);
    }
    function updateHOANumber($ID, $avail)
    {
        $dbh = new DB_Mysql();
            $stmt = $dbh->prepare("UPDATE properties_main 
                                      SET property_hoa_number=:2:
                                     WHERE property_id=:1:");
        $stmt->execute($ID, $avail);
    }
    function updateHOAManager($ID, $avail)
    {
        $dbh = new DB_Mysql();
            $stmt = $dbh->prepare("UPDATE properties_main 
                                      SET property_hoa_manager=:2:
                                     WHERE property_id=:1:");
        $stmt->execute($ID, $avail);
    }
    function updateSpa($ID, $avail)
    {
        $dbh = new DB_Mysql();
            $stmt = $dbh->prepare("UPDATE properties_main 
                                      SET property_spa=:2:
                                     WHERE property_id=:1:");
        $stmt->execute($ID, $avail);
    }
    function updateCvID($ID, $avail)
    {
        $dbh = new DB_Mysql();
            $stmt = $dbh->prepare("UPDATE properties_main 
                                      SET cv_id=:2:
                                     WHERE property_id=:1:");
        $stmt->execute($ID, $avail);
    }
    function getall_owners($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, CONCAT(lastname,', ',firstname) AS fullname
                                 FROM property_owners,
                                       contacts_main
                                WHERE property_id=:1:
                                  AND property_owners.contacts_id = contacts_main.contacts_id  ");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    static function getall_properties_by_status($status)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT cv_name, P.cv_id, cv_default_phone, cv_default_address, cv_default_city, cv_default_state, cv_default_zip, property_id, property_address, property_city, property_state, property_aptnum, property_zip, property_feepercent, property_maintenance 
                                  FROM properties_main as P
                                 LEFT JOIN cv_main ON P.cv_id = cv_main.cv_id
                                 WHERE property_status='$status'
                              ORDER BY P.property_address, BINARY P.property_aptnum");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    function getAllPropertyNotStatus($status)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT cv_name, P.cv_id, cv_default_phone, cv_default_address, cv_default_city, cv_default_state, cv_default_zip, property_id, property_address, property_aptnum, property_zip, property_feepercent, property_maintenance 
                                  FROM properties_main as P
                                 LEFT JOIN cv_main ON P.cv_id = cv_main.cv_id
                                 WHERE property_status!='$status'
                              ORDER BY P.property_address, BINARY P.property_aptnum");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    function get_id_to_property_hash()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                  FROM properties_main 
                              ORDER BY property_address");
        $stmt->execute();
        while($row = $stmt->fetch_assoc())
        {
            $arr[$row['property_id']] = $row;
        }
        return $arr;
    }

    function getall_properties_of_owner($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                 FROM property_owners
                           INNER JOIN properties_main
                                   ON properties_main.property_id=property_owners.property_id
                                WHERE property_owners.contacts_id = :1:  ");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    function linkfile($ID, $file, $property)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")
        {
            $stmt = $dbh->prepare("INSERT INTO properties_files 
                                           SET prop_to_file_id=:1:, 
                                               file_id=:2:, 
                                               property_id=:3:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE properties_files 
                                      SET file_id=:2:, 
                                          property_id=:3: 
                                    WHERE prop_to_file_id=:1:");
        }
        $stmt->execute($ID, $file, $property);
    }
    
    public static function delete_link($ID)
    {
    
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM properties_accounts WHERE propertyaccount_id=:1:");
        $stmt->execute($ID);
    }
    function delete_property($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM properties_main WHERE property_id=:1:");
        $stmt->execute($ID);
     }

    function getall_property_types()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM properties_type 
                            ORDER BY propertytype_name");
        $stmt->execute();
        return $stmt->fetchall_assoc();
     }
    function getall_accounts_of_property($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM properties_accounts 
                                LEFT JOIN transactions_accounts
                                ON properties_accounts.account_id = transactions_accounts.account_id
                                WHERE property_id=:1: ");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
     }

    function getall_files_of_property($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM properties_files as pf, files_main as fm
                                WHERE pf.property_id=:1: 
                                AND pf.file_id = fm.file_id");
        $stmt->execute($ID);
        $files = $stmt->fetchall_assoc();
        return $files;
    }
    function get_property_file($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT COUNT(*) as COUNT
                                FROM properties_files as pf
                                WHERE pf.file_id=:1:");
        $stmt->execute($ID);
        $files = $stmt->fetch_assoc();
        return $files;
    }
    public static function getall_settings()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM properties_module_settings ORDER BY setting_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function save_setting($name, $value)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO properties_module_settings
                                       SET setting_name=:1:, 
                                           setting_value=:2:");
        $stmt->execute($name, $value);
    }
    public static function delete_setting()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM properties_module_settings");
        $stmt->execute();
    }

}
?>
