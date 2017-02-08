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
class User_Session {
    private $php_session_id;
    private $native_session_id;
    private $dbhandle;
    private $logged_in;
    private $user_id;
    private $session_timeout = 10000;      # 1 hour inactivity timeout
    private $session_lifespan = 80000;    # 8 hour session duration
    private $session_death;
    private $dbh;
    
    public function __construct($dbh) 
    {
        $this->dbh = $dbh;
    	# Set up the handler
        session_set_save_handler(
            array(&$this, '_session_open_method'),
            array(&$this, '_session_close_method'),
            array(&$this, '_session_read_method'),
            array(&$this, '_session_write_method'),
            array(&$this, '_session_destroy_method'),
            array(&$this, '_session_gc_method')
        );
        
        # Check the cookie passed - if one is - if it looks wrong we'll scrub it right away
        $strUserAgent = $_SERVER["HTTP_USER_AGENT"];
        if (isset($_COOKIE['PHPSESSID'])) 
        {
            # Security and age check
            $test1 = false;
            $test2 = false;    
            $test3 = false;    
            $this->php_session_id = $_COOKIE['PHPSESSID'];
            $stmt = $this->dbh->prepare("SELECT session_id 
                                    FROM user_session 
                                    WHERE ascii_session_id = :1: 
                                    AND (now() - created) > :2: ");
            $stmt->execute($this->php_session_id, $this->session_lifespan);
            if($stmt->num_rows() > 0)
            {
                $test1 = true; 
                $this->session_death .= "SESSION LIFESPAN EXPIRED MORE THAN 8 HOURS OLD<BR />";
            }
            $stmt = $this->dbh->prepare("SELECT session_id 
                                    FROM user_session 
                                    WHERE ascii_session_id = :1: 
                                    AND ((now() - last_impression) >= :2:)");
            $stmt->execute($this->php_session_id, $this->session_timeout);
            if($stmt->num_rows() > 0) 
            {
                $test2 = true; 
                $this->session_death .= "INACTIVE FOR 60 MINUTES, SESSION TIMEOUT UP <BR />";
            }
            $stmt = $this->dbh->prepare("SELECT session_id 
                                    FROM user_session 
                                    WHERE ascii_session_id = :1: 
                                    AND (last_impression IS NULL)");
            $stmt->execute($this->php_session_id, $this->session_timeout);
            if($stmt->num_rows() > 0) 
            {
                $test2 = true; 
                $this->session_death .= "NEW SESSION, NO LAST IMPRESSION<BR />";
            }
            $stmt = $this->dbh->prepare("SELECT session_id 
                                    FROM user_session 
                                    WHERE ascii_session_id = :1: 
                                    AND user_agent != :2: ");
            $stmt->execute($this->php_session_id, $strUserAgent);
            if($stmt->num_rows() > 0) 
            {
                $test3 = true; 
                $this->session_death .= "Incorrect user agent <BR />";
            }
            if($test1 || $test2 || $test3) 
            {
                # Set failed flag
                $failed = 1;
                # Delete from database - we do garbage cleanup at the same time
                $stmt = $this->dbh->prepare("DELETE 
                                        FROM user_session 
                                        WHERE (ascii_session_id = :1:) 
                                        OR (now() - created) > :2:");
                $stmt->execute($this->php_session_id, $this->session_lifespan);
                # Clean up stray session variables
                $stmt = $this->dbh->prepare("DELETE 
                                        FROM session_variable 
                                        WHERE session_id NOT IN (SELECT id FROM user_session)");
                $stmt->execute();
                # Get rid of this one... this will force PHP to give us another
                unset($_COOKIE["PHPSESSID"]);
                $this->session_death .= 'SESSION FAILED START NEW SESSION  <BR />';
            };
        };
        # Set the life time for the cookie
        session_set_cookie_params(3153600);
        # Call the session_start method to get things started
        session_start();
    }

    public function Impress() 
    {
        if ($this->native_session_id) 
        {
            $stmt = $this->dbh->prepare("UPDATE user_session 
                                      SET last_impression = now() 
                                    WHERE session_id = " . $this->native_session_id);
            $stmt->execute();
        
        };
    }
    public function DeleteAllUsersSessions($id) 
    {
        $stmt = $this->dbh->prepare("DELETE FROM user_session WHERE user_id = :1: OR user_id = 0 OR user_id = NULL");
        $stmt->execute($id);
        //echo $stmt->query;
    }
    
    
    public function GetUserObject() {
           $objUser = new User($this->dbh, $this->user_id, $this->logged_in, $this->native_session_id);
           return($objUser);
    }
    
    public function GetSessionIdentifier() {
    return($this->php_session_id);
    }
    public function GetSessionDeath() {
    return($this->session_death);
    }
    


    public function __get($nm) 
    {
        $stmt = $this->dbh->prepare("SELECT variable_value 
                                FROM session_variable 
                                WHERE session_id = " . $this->native_session_id . " 
                                AND variable_name = '" . $nm . "'");
        $stmt->execute();
        if ($stmt->num_rows()>0) 
        {
            $row = $stmt->fetch_assoc();
            return(unserialize($row["variable_value"]));
        } else {
            return(false);
        };
    }


    public function __set($nm, $val) 
    {
        $strSer = serialize($val);
        $stmt = $this->dbh->prepare("INSERT INTO session_variable(session_id, variable_name, variable_value) 
                                    VALUES(" . $this->native_session_id . ", '$nm', '$strSer')");
        $stmt->execute();
    }
    
    private function _session_open_method($save_path, $session_name) 
    {
        # Do nothing
        return(true);
    }
    
    public function _session_close_method() 
    {
        # Do nothing
        return(true);
    }
    
    private function _session_read_method($id) 
    {
        # We use this to determine whether or not our session actually exists.
        $strUserAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->php_session_id = $id;
        # Set failed flag to 1 for now
        $failed = 1;
        # See if this exists in the database or not.
        $stmt = $this->dbh->prepare("SELECT session_id, logged_in, user_id, session_data FROM user_session WHERE ascii_session_id = '$id'");
        $stmt->execute();
        if ($stmt->num_rows()>0) 
        {
            $row = $stmt->fetch_assoc();
            $this->native_session_id = $row['session_id'];
            $this->user_id = $row['user_id'];
            if ($row['logged_in']=="Y") 
            {
                $this->logged_in = true;
            }
            else
            {
                $this->logged_in = false;
            };
            $stmt = $this->dbh->prepare("UPDATE user_session SET last_impression = now() WHERE ascii_session_id = '$id';");
            $stmt->execute();
            return $row['session_data'];
        } 
        else 
        {
            $this->logged_in = false;
            # We need to create an entry in the database
            $stmt = $this->dbh->prepare("INSERT INTO user_session(ascii_session_id, logged_in, user_id, created, user_agent) VALUES ('$id','N',0,now(),'$strUserAgent')");
            $stmt->execute();
            # Now get the true ID
            $stmt = $this->dbh->prepare("SELECT session_id FROM user_session WHERE ascii_session_id = '$id'");
            $stmt->execute();
            $row = $stmt->fetch_assoc();
            $this->native_session_id = $row["session_id"];
            $stmt = $this->dbh->prepare("UPDATE user_session SET last_impression = now() WHERE ascii_session_id = '$id';");
            $stmt->execute();
            return "";
         };
    }

    public function _session_write_method($id, $sess_data) 
    {
    	$stmt = $this->dbh->prepare("SELECT * FROM user_session
                  WHERE ascii_session_id = '$id'");
    	$stmt->execute();
    	if($stmt->num_rows() > 0) {
    		// ...update session-data
    		$query = "
                UPDATE user_session
                SET session_data = '".$stmt->dbh->real_escape_string($sess_data)."',
                    last_impression = now()
                WHERE ascii_session_id = '" . $id . "'
            ";
    	}
    	// if no session-data was found,
    	else {
    		// create a new row
    		$query = "INSERT INTO user_session (
                        session_data,
                        ascii_session_id,
                        last_impression)
                    VALUES(
                        '".$stmt->dbh->real_escape_string($sess_data)."',
                        '".$id."',
                        now())";
    	}
    	//global $DB_SETTINGS;
        //mysql_connect($DB_SETTINGS['dbhost'],$DB_SETTINGS['user'],$DB_SETTINGS['pass']);
        //mysql_select_db($DB_SETTINGS['dbname']); 
        // new session-expire-time
        //$newExp = time() + $this->lifeTime;
        // is a session with this id in the database?
        //$query = "SELECT * FROM user_session
        //          WHERE ascii_session_id = '$id'";
        //$result = mysql_query($query);
        // if yes,
        /*if($stmt->num_rows() > 0) {
            // ...update session-data
            $query = "
                UPDATE user_session
                SET session_data = '".mysql_real_escape_string($sess_data)."',
                    last_impression = now()
                WHERE ascii_session_id = '" . $id . "' 
            ";
        }
        // if no session-data was found,
        else {
            // create a new row
            $query = "INSERT INTO user_session (
                        session_data,
                        ascii_session_id,
                        last_impression)
                    VALUES(
                        '".mysql_real_escape_string($sess_data)."',
                        '".$id."',
                        now())";
        }
        */
        $stmt = $this->dbh->prepare($query);
    	$stmt->execute();
    	return(true);
        
    }
    
    private function _session_destroy_method($id) 
    {
        $stmt = $this->dbh->prepare("DELETE FROM user_session WHERE ascii_session_id = '$id'");
        $stmt->execute();
        return($stmt);
    }
    
    private function _session_gc_method($maxlifetime) 
    {
        return(true);
    }
    
    
}




?>
