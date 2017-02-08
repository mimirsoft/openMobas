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
class Timecard_IP {
    
    public function addIP($ip)
    {
    	$decimal_ip = framework::inet_ptod($ip)	;
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO timecard_ips 
        				SET ip = :1:");
        $stmt->execute($decimal_ip);
    }    
    public function deleteIP($ip)
    {
        $decimal_ip = framework::inet_ptod($ip)	;
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM timecard_ips
        					WHERE ip = :1:");
        $stmt->execute($decimal_ip);
    }    
    
    
    public function getAllIPs()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM timecard_ips");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }    
    
    public function checkIP($ip)
    {
    	//echo $ip;
    	$decimal_ip = framework::inet_ptod($ip)	;
        //echo "----------".$decimal_ip;
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                FROM timecard_ips 
                                WHERE ip = :1:
                                LIMIT 1");
        $stmt->execute($decimal_ip);
        //echo $stmt->query;
        if ($stmt->num_rows()>0) {
        	return true;
        }
		else
		{
			return false;
		}    	
    }
    
}



?>
