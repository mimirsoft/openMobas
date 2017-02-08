<?php
/*
 * 
 This file is part of OpenMobas
Copyright (C) 2011, Kevin Milhoan, Mimir Software Corporation

OpenMobas is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

OpenMobas is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

*
*/
class Admin_SystemException extends Exception{
	public $message;
	public function __construct($message)
	{
		$this->message = $message;
	}
}

class Admin_System {
	private $dbh;
	private $user;
	private $rbac_user;
	
	public function __construct($dbh, $user, $rbac_user)
	{
	
		$this->dbh = $dbh;
		$this->user = $user;
		$this->rbac_user = $rbac_user;
	
	}
	
	
	public function update_system($ID, $name, $system_array)
	{
	    $system_array = serialize($system_array);
	    if($ID == "NULL")// If it is a new entry.
	    {
	        $stmt = $this->dbh->prepare("INSERT INTO admin_system
	                                       SET system_id=:1:, 
	                                           system_name=:2:, 
	                                           system_array=:3:");
	    }
	    else//If it is an edit to an existing entry
	    {
	        $stmt = $this->dbh->prepare("UPDATE admin_system 
	                                  SET system_name=:2:, 
	                                      system_array=:3:
	                                WHERE system_id=:1:");
	    }
	    $stmt->execute($ID, $name, $system_array);
	}
	
	public function get_systems()
	{
	    $stmt = $this->dbh->prepare("SELECT * FROM admin_system");
	    $stmt->execute();
	    $sys = $stmt->fetchall_assoc();
	    return $sys;
	}
	
	
	public function install_module($module)
	{
		include("../$module/{$module}_install.php");
		//if it isn't installed already, and all dependecies are installed, then we can install it
		
		//first check dependencies
		//if $dependancy is not empty
		if(sizeof($dependencies) > 0 )
		{
			foreach($dependencies as $value)
			{
				//check if dependency is installed
				$installed = $this->isInstalled($value);
				//if not, throw error
				if(!installed)
				{	
					throw new Admin_SystemException($value." module not installed.  Module ".$module." requires it. Please install first");
				}
			}
		}

		//check for record in modules table
		$stmt = $this->dbh->prepare("SELECT * 
				                       FROM modules
					                  WHERE module_name=:1:");
		$stmt->execute($module);
		$modules = $stmt->fetchall_assoc();
		if(sizeof($modules) > 0 )
		{
			// if record found, check if tables exist
			foreach($tables as $table)
			{
				//if table(s) does not exist, then delete record in module table
				$result = mysql_query("SHOW TABLES LIKE 'myTable'");
				$tableExists = mysql_num_rows($result) > 0;
				if(!$tableExists)
				{
					$this->deleteModule();
				}
			}
		}
		else{
			//if no record
			//check if table exists anyway
			foreach($tables as $table)
			{
				$result = mysql_query("SHOW TABLES LIKE '$table'");
				$tableExists = mysql_num_rows($result) > 0;
				//if it does, throw error, tell the user to upgrade instead
				if($tableExists)
				{
					throw new Admin_SystemException("Table ".$table." for module ".$module." already exists. Run upgrade instead.");
				}
			}
			
		}
		
		// perform new installation
		//install module
		//create tables
		//add entry into modules tables
		

		//check for tables
		//if not there, install
 
	
	}
	public function upgrade_module($module)
	{
		//check for existing version
		//update tables
		//update modules table with record of new version
		

		//update
		//check for version in modules tables
		//select from modules name=$module
		//compare version to table
		//check dependencies
		//are they installed?
		//if not, ask for them to be installed
		//end, throw error message
		
	}

}

?>