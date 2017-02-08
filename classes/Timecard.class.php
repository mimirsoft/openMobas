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
class Timecard {
    
	private $clocked_in;
	private $clock_in;
	private $user_id;
    
    public function __construct($user_id) 
    {
    	$this->user_id = $user_id;
        //is the user clocked in?
    	$dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                FROM timecard_main 
                                WHERE user_id = :1:
                                AND clock_out IS NULL
                                LIMIT 1");
        $stmt->execute( $this->user_id);
        if ($stmt->num_rows()>0) {
        	
        	$row = $stmt->fetch_assoc();
        	$this->clocked_in = true;
        	$this->clock_in = $row['clock_in'];
        }

    }
    public function getUserId()
    {
        return $this->user_id;
    }
    public function getClockIn()
    {
        return $this->clock_in;
    }
    public function clockIn()
    {
        if(!($this->clocked_in))
        {
	        $dbh = new DB_Mysql();
	        $stmt = $dbh->prepare("INSERT INTO timecard_main
	                                        SET user_id=:1:");
	        $stmt->execute($this->user_id);
	        $this->clocked_in = true;
            $stmt = $dbh->prepare("SELECT *
                                    FROM timecard_main 
                                    WHERE user_id = :1:
                                    AND clock_out IS NULL
                                    LIMIT 1");
            $stmt->execute($this->user_id);
            if ($stmt->num_rows()>0) {
            	
            	$row = $stmt->fetch_assoc();
            	$this->clock_in = $row['clock_in'];
            }
	        
        }
    }
    public function clockOut()
    {
        if($this->clocked_in)
        {
	        $dbh = new DB_Mysql();
	        $stmt = $dbh->prepare("UPDATE timecard_main
	        					 SET clock_out = NOW()
	        					 WHERE user_id=:1:
	        					 AND clock_out IS NULL");
	        $stmt->execute($this->user_id);
	        $this->clocked_in = false;
	        return mysql_insert_id();
        }
        
    }    
    public function editCard($timecard_id, $clock_in, $clock_out)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                FROM timecard_main 
                                WHERE timecard_id =:1:
                                LIMIT 1");
        $stmt->execute($timecard_id);
        //echo $stmt->query;
        $card = $stmt->fetch_assoc();
        $stmt = $dbh->prepare("INSERT INTO timecard_edits
	                                        SET user_id=:1:,
	                                        oldclock_in=:2:,
	                                        newclock_in=:3:,
	                                        oldclock_out=:4:,
	                                        newclock_out=:5:,
	                                        timecard_id=:6:");
        $stmt->execute($this->user_id, $card['clock_in'], $clock_in, $card['clock_out'], $clock_out, $timecard_id);
        //echo $stmt->query;
        $stmt = $dbh->prepare("UPDATE timecard_main
	        					 SET clock_in = :2:,
	        					 clock_out = :3:
	        					 WHERE timecard_id=:1:");
        $stmt->execute($timecard_id, $clock_in, $clock_out);
        //echo $stmt->query;
        
    }    
    
    
    public function isClockedIn()
    {
    	return $this->clocked_in;
    }

    public function getAllClockIns()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM timecard_main
                                        WHERE user_id=:1:");
        $stmt->execute($this->user_id);
        return $stmt->fetchall_assoc();
    }
    public function getAllClockInsBetweenDates($start, $end)
    {
        $end .= " 23:59:59";
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM timecard_main
                                        WHERE user_id=:1:
                                        AND clock_in > TIMESTAMP(:2:)
                                        AND clock_in < :3:");
        $stmt->execute($this->user_id, $start, $end);
        //echo $stmt->query;
        return $stmt->fetchall_assoc();
    }
    public function sumClockInsBetweenDates($start, $end)
    {
        $end .= " 23:59:59";
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))) AS seconds  FROM timecard_main
                                        WHERE user_id=:1:
                                        AND clock_in > TIMESTAMP(:2:)
                                        AND clock_in < TIMESTAMP(:3:)");
        $stmt->execute($this->user_id, $start, $end);
        //echo $stmt->query;
        return $stmt->fetch_assoc();
    }    
    public function getAllEditsToTimecard($timecard_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM timecard_edits
        						LEFT JOIN user_main
        						ON timecard_edits.user_id = user_main.user_id
                                        WHERE timecard_id=:1:");
        $stmt->execute($timecard_id);
        return $stmt->fetchall_assoc();
    }
 

}



?>
