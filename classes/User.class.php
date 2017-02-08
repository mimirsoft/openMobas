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

class User {
    private $native_session_id;
    private $logged_in;
    private $user_id;
    private $username;
    private $user_lastname;
    private $user_firstname;
    private $user_fullname;
    private $password_hash;
    private $email;
    private $telephone;
    private $permissions;
    private $limited_permissions;
    private $dbh;
    
    
    public function print_native()
    {
        echo $this->native_session_id;
    }

    public function __construct($dbh, $id, $logged_in, $native_session_id) 
    {
        
    	$this->dbh = $dbh;
    	if($id != '')
    	{
	    	$stmt = $this->dbh->prepare("SELECT *
	                                FROM user_main 
	                                WHERE user_id = '$id'");
	        $stmt->execute();
	        $row = $stmt->fetch_assoc();
	        $this->username = $row['username'];
	        $this->user_lastname = $row['user_lastname'];
	        $this->user_firstname = $row['user_firstname'];
	        $this->user_fullname = $row['user_firstname']." ".$row['user_lastname'];
	        $this->password_hash = $row['password_hash'];
	        $this->email = $row['user_email'];
	        $this->telephone = $row['user_default_phone'];
    	}
        $this->user_id = $id;
        $this->logged_in = $logged_in;
        $this->native_session_id = $native_session_id;
        
    }
    
public function get_user_from_id($ID)
{
    $stmt = $this->dbh->prepare("SELECT * FROM user_main WHERE user_id=:1:");
    $stmt->execute($ID);
    return $stmt->fetch_assoc();
}
public function get_user_from_email($email)
{
    $stmt = $this->dbh->prepare("SELECT * FROM user_main WHERE user_email=:1:");
    $stmt->execute($email);
    if($stmt->num_rows() > 0)
    {
       return $stmt->fetch_assoc();
    }
    else{
      return false;
    
    }    
}
public function get_user_from_confirm_code($code)
{
    $stmt = $this->dbh->prepare("SELECT * FROM user_main WHERE confirm_code=:1:");
    $stmt->execute($code);
    if($stmt->num_rows() > 0)
    {
       return $stmt->fetch_assoc();
    }
    else{
      return false;
    
    }    
}
public function create_user($ID, $username, $pass, $last_name, $first_name, $email, $default_phone)
{
	$pass_hash = $this->createPasswordHash($pass);
	$stmt = $this->dbh->prepare("INSERT INTO user_main 
                                       SET user_id=:1:, 
                                           user_lastname=:2:, 
                                           user_firstname=:3:, 
                                           password_hash=:4:, 
                                           username=:5:,
                                           user_email=:6:,
                                           user_default_phone=:7:");
    $stmt->execute($ID, $last_name, $first_name, $pass_hash, $username, $email, $default_phone);
    
    return $stmt->dbh->insert_id;
}

//create the salt, should only be used by createPasswordHash
private function getPasswordSalt()
{
	return mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
}
//create the Hash, should only be used when changing password or creating user first time
function createPasswordHash($password)
{
	$options = [
	'cost' => 11,
	'salt' => User::getPasswordSalt()
	];
	//echo "SALT<BR/>";
	//echo $options['salt'];
	//echo "<BR/>";
	$hash = password_hash($password, PASSWORD_BCRYPT, $options);
	return $hash;
}

function comparePassword($password, $hash)
{
	//echo "comparing ".$password." AND ".$hash;
	return password_verify($password, $hash);
}


//this does not update the password
public function update_user($ID, $username, $last_name, $first_name, $email, $default_phone)
{
    $stmt = $this->dbh->prepare("UPDATE user_main 
                                  SET user_lastname=:2:, 
                                      user_firstname=:3:, 
                                      username=:4:,
                                      user_email=:5:,
                                      user_default_phone=:6: 
                                WHERE user_id=:1:");
    $stmt->execute($ID, $last_name, $first_name,  $username, $email, $default_phone);
    return $stmt->dbh->insert_id;
}

//this sets the password
public function set_password($ID, $new_pw)
{
    $new_pw = $this->createPasswordHash($new_pw);
    $stmt = $this->dbh->prepare("UPDATE user_main 
                                  SET password_hash=:2:                                      
                                WHERE user_id=:1:");
    $stmt->execute($ID, $new_pw);
    return true;
}

//this is callable by the user and contains verification of match
//this function is wrong, and the compare needs to be fixed
public function udpdate_user_password($ID, $old_pw, $new_pw, $new_pw2)
{
    $old_pw = $this->createPasswordHash($old_pw);
    $user = $this->get_user_from_id($ID);
    if(!$this->comparePassword($old_pw, $user['password_hash']))
    {
        $err = "Password incorrect";
        return $err;
    }
    if($new_pw == '')
    {
        $err = 'New Password not supplied';
        return $err;
    }
    if($new_pw != $new_pw2)
    {
        $err = "Passwords do not match";
        return $err;
    }
    $this->set_password($ID, $new_pw);
    return;
}

public function update_user_info($ID, $last_name, $first_name)
{
    $stmt = $this->dbh->prepare("UPDATE user_main 
                                  SET user_lastname=:2:, 
                                      user_firstname=:3:                              
                                WHERE user_id=:1:");
    $stmt->execute($ID, $last_name, $first_name);
    return;
}

public function get_temp_users()
{
    $stmt = $this->dbh->prepare("SELECT * FROM temp_members_db");
    $stmt->execute();
    $users = $stmt->fetchall_assoc();
    return $users;
}

public function update_interested_item($ID, $item)
{
    $stmt = $this->dbh->prepare("UPDATE user_main 
                                  SET interested_item=:2:
                                WHERE user_id=:1:");
    $stmt->execute($ID, $item);
    return;
}
public function getall_users_interested_in_item($ID)
{
    $stmt = $this->dbh->prepare("SELECT * 
                             FROM user_main 
                            WHERE interested_item=:1:");
    $stmt->execute($ID);
    $users = $stmt->fetchall_assoc();
    return $users;
}
public function update_confirm_code($ID, $code)
{
    $stmt = $this->dbh->prepare("UPDATE user_main 
                                  SET confirm_code=:2:
                                WHERE user_id=:1:");
    //if it worked, this will return true!!
    return $stmt->execute($ID, $code);
    
}


function delete_user($ID)
{
    $stmt = $this->dbh->prepare("DELETE FROM user_main 
                                 WHERE user_id=:1:");
    $stmt->execute($ID);
}

public function get_users($sortby)
{
    $stmt = $this->dbh->prepare("SELECT * FROM user_main ORDER BY $sortby");
    $stmt->execute($sortby);
    $users = $stmt->fetchall_assoc();
    return $users;
}

    
    public function Login($strUsername, $strPlainPassword) 
    {
    	$stmt = $this->dbh->prepare("SELECT user_id, username, password_hash, user_email
                                FROM user_main 
                                WHERE username=:1: 
    			                      LIMIT 1");
    	$stmt->execute($strUsername);
    	$row = $stmt->fetch_assoc();
    	//echo $strPlainPassword;
    	//print_r($row);
    	if($this->comparePassword($strPlainPassword, $row['password_hash']))
    	{
    		//echo "PASSWORD MATCH";
    		$this->user_id = $row["user_id"];
    		$this->username = $row["username"];
    		$this->email = $row['user_email'];
    		$this->logged_in = true;
    		$stmt = $this->dbh->prepare("UPDATE user_session
                                    SET logged_in = 'Y',
                                        session_data = '',
                                        user_id = " . $this->user_id . "
                                  WHERE session_id = " . $this->native_session_id);
    		$stmt->execute();
    		$decimal_ip = framework::inet_ptod($_SERVER['REMOTE_ADDR'])	;
    		$stmt = $this->dbh->prepare("UPDATE user_main
    				SET ip = $decimal_ip,
    				last_login = now()
    				WHERE user_id = " . $this->user_id);
    				$stmt->execute();
    		return(true);
    	} else {
    		
    		return(false);
    	};
   }

    public function LogOut() {
    if ($this->logged_in == true) {
        $stmt = $this->dbh->prepare("UPDATE user_session 
                                  SET logged_in = 'N', 
                                      user_id = 0 
                                WHERE session_id = " . $this->native_session_id);
        $stmt->execute();
        $this->logged_in = false;
        $this->username = "";
        $this->user_id = 0;
        return(true);
    } else {
        return(false);
    };
    }
    
    public function GetUserID() {
    if ($this->logged_in) {
        return($this->user_id);
    } else {
        return(false);
    };
    }
    
    public function GetUserID_no_test() {
        return($this->user_id);
    }

    public function GetUserName() {
    return($this->username);
    }
    public function GetUserFullName() {
    return($this->user_fullname);
    }
    public function GetEncryptedPassword() {
    return($this->password);
    }
    public function GetFirstname() {
    return($this->user_firstname);
    }
    public function GetLastname() {
    return($this->user_lastname);
    }
    public function GetTelephone() {
    return($this->telephone);
    }
    public function GetEmail() {
    return($this->email);
    }
    public function IsLoggedIn() {
    return($this->logged_in);
    }
    function BuildPermissions() {
        global $INSTALLED_MODULES;
        global $MODULE_NAME;
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM user_permissions 
                                WHERE user_id='".$this->GetUserID()."' ");
        $stmt->execute();
        $dbRow = $stmt->fetch_assoc();
        foreach($INSTALLED_MODULES as $value)
        {
            //We include the @ incase the module is not registered, but is installed
            @$this->permissions[$value['mod_code']]=$dbRow['mod_'.$value['mod_code']];
        }
        $this->permissions['module_none'] = "U";
        $stmt = $this->dbh->prepare("SELECT * 
                                 FROM user_limited 
                                WHERE user_id='".$this->GetUserID()."'
                                AND context_module='".$MODULE_NAME."' ");
        $stmt->execute();
        while($dbRow = $stmt->fetch_assoc())
        {
            $this->limited_permissions[$MODULE_NAME][$dbRow['element_module']][$dbRow['element_table']][$dbRow['element_id']] = 1;
        }
    }

    public function CheckPermission($module) {
        if ($this->permissions[$module] == "U" ||
            $this->permissions[$module] == "L" 
            ) {
            return(true);
        } 
        else {
            return(false);
        }
    }
    public function CheckPermissionType($module) {
        return $this->permissions[$module];
    }
    public function GetLimitedPermissions($module) {
        return $this->limited_permissions[$module];
    }
}


?>