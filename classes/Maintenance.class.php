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
class Maintenance{

    public static function add_maintenance($ID, $title, $txt, $user, $i_id)
    {
        @$user_object = new USER($user);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO maintenance_main
                                       SET maintenance_id=:1:, 
                                           maintenance_title=:2:, 
                                           maintenance_txt=:3:, 
                                           closed_yn='NO', 
                                           whoopen_id=:4:,
                                           whoopen_username=:6:,  
                                           whenopen_date=NOW(),
                                           whenfollowup=NOW(),
                                           inventory_id=:5:");
        $stmt->execute($ID, $title, $txt, $user, $i_id, $user_object->GetUsername());
        $caseID = mysql_insert_id();
        return $caseID;
        //casesystem::add_entry("NULL", $comments, $hiddencomments, $caseID, $user);
    }
    public static function update_maintenance($ID, $title, $txt)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE maintenance_main
                                  SET maintenance_title=:2:,
									  maintenance_txt=:3:
                                WHERE maintenance_id=:1:");
        $stmt->execute($ID, $title, $txt);
    }
    public static function update_action_needed($ID, $title)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE maintenance_main
                                  SET action_needed=:2:
                                WHERE maintenance_id=:1:");
        $stmt->execute($ID, $title);
    }
    public static function checkFollowUp()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE maintenance_main 
                                  SET action_needed=1                                           
                                WHERE NOW()>whenfollowup
                                AND closed_yn='NO'");
        $stmt->execute();
        
    }    
    public static function setFollowUp($caseID, $followup)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE maintenance_main 
                                  SET whenfollowup=:1:
                                WHERE maintenance_id=:2: ");
        $stmt->execute($followup, $caseID);
        
    }
    public static function setUpdated($caseID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE maintenance_main 
                                  SET whenupdate=NOW()
                                WHERE maintenance_id=:1: ");
        $stmt->execute($caseID);
        
    }
    
    public static function update_customer_id($ID, $customer)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE maintenance_main
                                  SET customer_id=:2:
                                WHERE maintenance_id=:1:");
        $stmt->execute($ID, $customer);
    }
    public static function close_maintenance($ID, $user)
    {
        $user_object = @new USER($user);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE maintenance_main
                                  SET whoclosed_id=:2:, 
                                      whoclosed_username=:3:, 
                                      whenclosed_date=NOW(), 
                                      closed_yn='YES'
                                WHERE maintenance_id=:1:");
        $stmt->execute($ID, $user, $user_object->GetUsername());
    }
    public static function reopen_maintenance($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE maintenance_main
                                  SET whoclosed_id=NULL, 
                                      whoclosed_username=NULL, 
                                      whenclosed_date=NULL, 
                                      closed_yn='NO'
                                WHERE maintenance_id=:1:");
        $stmt->execute($ID);
    }
    public static function add_case($ID, $case_id, $maintenance_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO maintenance_cases
                                  SET maintenance_case_id=:1:, 
                                      case_id=:2:, 
                                      maintenance_id=:3:");
        $stmt->execute($ID, $case_id, $maintenance_id);
    }

    public static function get_maintenance($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM maintenance_main WHERE maintenance_id=:1: LIMIT 1");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function getAllForInventoryID($ID, $sort)
    {
        $orderby = 'whenopen_date';
        if($sort == 'whenopen_date')
        {
            $orderby = 'whenopen_date';
        }
        if($sort == 'maintenance_title')
        {
            $orderby = 'maintenance_title';
        }
        if($sort == 'whoopen_id')
        {
            $orderby = 'whoopen_id';
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, maintenance_main.maintenance_id, GROUP_CONCAT(MTC.cv_name) AS vendor_string
        					 	 FROM maintenance_main 
         				   INNER JOIN inventory_items 
                                   ON inventory_items.inventory_id = maintenance_main.inventory_id 
                            LEFT JOIN (SELECT maintenance_mtm_cv_main.maintenance_id, cv_main.cv_name FROM maintenance_mtm_cv_main INNER JOIN cv_main on cv_main.cv_id = maintenance_mtm_cv_main.cv_id) AS MTC
           	                       ON maintenance_main.maintenance_id = MTC.maintenance_id
                                WHERE maintenance_main.inventory_id=:1:
                             GROUP BY maintenance_main.maintenance_id
    	                     ORDER BY :2:");
        $stmt->execute($ID, $orderby);
        return $stmt->fetchall_assoc();
    }
    public static function getAllOpenForInventoryID($ID, $sort)
    {
        $orderby = 'whenopen_date';
        if($sort == 'whenopen_date')
        {
            $orderby = 'whenopen_date';
        }
        if($sort == 'maintenance_title')
        {
            $orderby = 'maintenance_title';
        }
        if($sort == 'whoopen_id')
        {
            $orderby = 'whoopen_id';
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, maintenance_main.maintenance_id, GROUP_CONCAT(MTC.cv_name) AS vendor_string
        					 	 FROM maintenance_main 
         				   INNER JOIN inventory_items 
                                   ON inventory_items.inventory_id = maintenance_main.inventory_id 
                            LEFT JOIN (SELECT maintenance_mtm_cv_main.maintenance_id, cv_main.cv_name FROM maintenance_mtm_cv_main INNER JOIN cv_main on cv_main.cv_id = maintenance_mtm_cv_main.cv_id) AS MTC
           	                       ON maintenance_main.maintenance_id = MTC.maintenance_id
                                WHERE maintenance_main.inventory_id=:1:
                                  AND closed_yn='NO'
                             GROUP BY maintenance_main.maintenance_id
    	                     ORDER BY :2:");
        $stmt->execute($ID, $orderby);
        return $stmt->fetchall_assoc();
    }
    public static function getAllClosedForInventoryID($ID, $sort)
    {
        $orderby = 'whenopen_date';
        if($sort == 'whenopen_date')
        {
            $orderby = 'whenopen_date';
        }
        if($sort == 'maintenance_title')
        {
            $orderby = 'maintenance_title';
        }
        if($sort == 'whoopen_id')
        {
            $orderby = 'whoopen_id';
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, maintenance_main.maintenance_id, GROUP_CONCAT(MTC.cv_name) AS vendor_string
        					 	 FROM maintenance_main 
         				   INNER JOIN inventory_items 
                                   ON inventory_items.inventory_id = maintenance_main.inventory_id 
                            LEFT JOIN (SELECT maintenance_mtm_cv_main.maintenance_id, cv_main.cv_name FROM maintenance_mtm_cv_main INNER JOIN cv_main on cv_main.cv_id = maintenance_mtm_cv_main.cv_id) AS MTC
           	                       ON maintenance_main.maintenance_id = MTC.maintenance_id
                                WHERE maintenance_main.inventory_id=:1:
                              	  AND closed_yn='YES'
                             GROUP BY maintenance_main.maintenance_id
    	                     ORDER BY :2:");
        $stmt->execute($ID, $orderby);
        return $stmt->fetchall_assoc();
    }
    public static function getAllOpen($sort)
    {
        $orderby = 'whenopen_date';
        if($sort == 'whenopen_date')
        {
            $orderby = 'whenopen_date';
        }
        if($sort == 'maintenance_title')
        {
            $orderby = 'maintenance_title';
        }
        if($sort == 'whoopen_id')
        {
            $orderby = 'whoopen_id';
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, maintenance_main.maintenance_id, GROUP_CONCAT(MTC.cv_name) AS vendor_string FROM maintenance_main 
         				INNER JOIN inventory_items 
                  ON inventory_items.inventory_id = maintenance_main.inventory_id 
                  LEFT JOIN (SELECT maintenance_mtm_cv_main.maintenance_id, cv_main.cv_name FROM maintenance_mtm_cv_main INNER JOIN cv_main on cv_main.cv_id = maintenance_mtm_cv_main.cv_id) AS MTC
           	                       ON maintenance_main.maintenance_id = MTC.maintenance_id
	   	                       WHERE closed_yn='NO'
    	                       GROUP BY maintenance_main.maintenance_id
    	                       ORDER BY :1:");
        $stmt->execute($orderby);
        return $stmt->fetchall_assoc();
    }
    public static function getAllClosed($sort)
    {
        $orderby = 'whenopen_date';
        
        if($sort == 'whenopen_date')
        {
            $orderby = 'whenopen_date';
        }
        if($sort == 'maintenance_title')
        {
            $orderby = 'maintenance_title';
        }
        if($sort == 'whoopen_id')
        {
            $orderby = 'whoopen_id';
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, maintenance_main.maintenance_id, GROUP_CONCAT(MTC.cv_name) AS vendor_string FROM maintenance_main 
         				INNER JOIN inventory_items 
                  ON inventory_items.inventory_id = maintenance_main.inventory_id 
                  LEFT JOIN (SELECT maintenance_mtm_cv_main.maintenance_id, cv_main.cv_name FROM maintenance_mtm_cv_main INNER JOIN cv_main on cv_main.cv_id = maintenance_mtm_cv_main.cv_id) AS MTC
           	                       ON maintenance_main.maintenance_id = MTC.maintenance_id
	   	                       WHERE closed_yn='YES'
    	                       GROUP BY maintenance_main.maintenance_id");
        $stmt->execute($orderby);
        return $stmt->fetchall_assoc();
    }
    public static function getAll($sort)
    {
        $orderby = 'whenopen_date';
        
        if($sort == 'whenopen_date')
        {
            $orderby = 'whenopen_date';
        }
        if($sort == 'maintenance_title')
        {
            $orderby = 'maintenance_title';
        }
        if($sort == 'whoopen_id')
        {
            $orderby = 'whoopen_id';
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, maintenance_main.maintenance_id, GROUP_CONCAT(MTC.cv_name) AS vendor_string FROM maintenance_main 
         				INNER JOIN inventory_items 
                  ON inventory_items.inventory_id = maintenance_main.inventory_id 
                  LEFT JOIN (SELECT maintenance_mtm_cv_main.maintenance_id, cv_main.cv_name FROM maintenance_mtm_cv_main INNER JOIN cv_main on cv_main.cv_id = maintenance_mtm_cv_main.cv_id) AS MTC
           	                       ON maintenance_main.maintenance_id = MTC.maintenance_id
    	                       GROUP BY maintenance_main.maintenance_id");
        $stmt->execute($orderby);
        return $stmt->fetchall_assoc();
    }
    public static function get_maintenance_cases($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM maintenance_cases 
                        LEFT JOIN casesystem_main
                        ON  maintenance_cases.case_id = casesystem_main.case_id
                        WHERE maintenance_id=:1: ");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function add_maintenance_mtm_contacts($maintenance, $contacts)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO maintenance_mtm_contacts 
                                       SET contacts_id=:1:, 
                                           maintenance_id=:2: ");

        try{
             $stmt->execute($contacts, $maintenance);
        }
        catch(MysqlException $exception)
        {
            throw new CVException("Maintenance ticket already tagged with contact ".$contacts);
        }

    }
    public static function addMaintenanceMtmCVMain($maintenance, $contacts)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO maintenance_mtm_cv_main 
                                       SET cv_id=:1:, 
                                           maintenance_id=:2: ");

        try{
             $stmt->execute($contacts, $maintenance);
        }
        catch(MysqlException $exception)
        {
            throw new CVException("Maintenance ticket already tagged with vendor ".$contacts);
        }

    }
    public static function removeMaintenanceMtmCVMain($maintenance, $contacts)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM maintenance_mtm_cv_main 
                                       WHERE cv_id=:1: 
                                       AND 
                                           maintenance_id=:2: ");

         $stmt->execute($contacts, $maintenance);
    }
    public static function get_contacts_of_maintenance($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM maintenance_mtm_contacts 
                           INNER JOIN contacts_main 
                           ON contacts_main.contacts_id = maintenance_mtm_contacts.contacts_id
                           WHERE maintenance_mtm_contacts.maintenance_id=:1: 
                           ORDER BY lastname");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }   
    public static function getVendorsOnTicket($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM maintenance_mtm_cv_main 
                           INNER JOIN cv_main 
                           ON cv_main.cv_id = maintenance_mtm_cv_main.cv_id
                           WHERE maintenance_mtm_cv_main.maintenance_id=:1: 
                           ORDER BY cv_name");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }  }
?>
