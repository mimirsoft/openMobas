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
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
 * 
 * 
 * Below is the basic framework to function, the admin system, the rbac, user and session tables
 * 
 * 
 */

require_once("../../../../openMobas/globalconstants.php");

require_once("../../../../openMobas/classes/Database_Mysql.class.php");


$mysqli = new mysqli($DB_SETTINGS['dbhost'], $DB_SETTINGS['user'], $DB_SETTINGS['pass'], $DB_SETTINGS['dbname']);


//$create = "CREATE DATABASE "
//$stmt = $dbh->prepare($setup);
//$stmt->execute();

$setup = "
		

--
-- Table structure for table `user_main`
--
DROP TABLE IF EXISTS `user_session`;
		
CREATE TABLE `user_main` (
  `user_id` int(11) NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `password_hash` varchar(240) default NULL,
  `user_firstname` varchar(64) default NULL,
  `user_lastname` varchar(64) default NULL,
  `user_email` varchar(75) default NULL,
  `user_default_phone` varchar(75) default NULL,
  `theme` varchar(60) NOT NULL default 'default',
  `language` varchar(60) NOT NULL default 'English',
  `lastactive` int(60) NOT NULL default '0',
  `pager_email` varchar(255) default NULL,
  `interested_item` int(11) NOT NULL,
  `confirm_code` varchar(80) NOT NULL default '',
  `ip` decimal(39) unsigned default NULL,
  `last_login` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_session`
--

CREATE TABLE `user_session` (
  `session_id` int(11) NOT NULL auto_increment,
  `ascii_session_id` varchar(32) default NULL,
  `logged_in` enum('Y','N') NOT NULL default 'N',
  `user_id` int(11) default NULL,
  `last_impression` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `user_agent` varchar(255) default NULL,
  `session_data` mediumtext character set utf8 collate utf8_unicode_ci,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `session_variable`
--

CREATE TABLE `session_variable` (
  `id` int(11) NOT NULL auto_increment,
  `session_id` int(11) default NULL,
  `variable_name` varchar(64) default NULL,
  `variable_value` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--		
		
CREATE TABLE `admin_system` (
`system_id` int(11) NOT NULL auto_increment,
`system_name` varchar(30) NOT NULL default '',
`system_array` text NOT NULL,
PRIMARY KEY  (`system_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `rbac_actions`
--

CREATE TABLE `rbac_actions` (
  `action_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `description` text,
  PRIMARY KEY  (`action_id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rbac_domains`
--

CREATE TABLE `rbac_domains` (
  `domain_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(40) default NULL,
  `description` text,
  `is_singular` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`domain_id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rbac_domains_has_objects`
--

CREATE TABLE `rbac_domains_has_objects` (
  `id` int(11) NOT NULL auto_increment,
  `domain_id` int(10) unsigned NOT NULL default '0',
  `object_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `domain_mtm_object` (`domain_id`,`object_id`),
  KEY `fk_domains_has_objects_domains` (`domain_id`),
  KEY `fk_domains_has_objects_objects` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rbac_objects`
--

CREATE TABLE `rbac_objects` (
  `object_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `description` text,
  PRIMARY KEY  (`object_id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rbac_privileges`
--

CREATE TABLE `rbac_privileges` (
  `privilege_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `description` text,
  `is_singular` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`privilege_id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rbac_privileges_has_actions`
--

CREATE TABLE `rbac_privileges_has_actions` (
  `id` int(11) NOT NULL auto_increment,
  `privilege_id` int(10) unsigned NOT NULL default '0',
  `action_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `privilege_mtm_action` (`privilege_id`,`action_id`),
  KEY `fk_privileges_has_actions_actions` (`action_id`),
  KEY `fk_privileges_has_actions_privileges` (`privilege_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rbac_roles`
--

CREATE TABLE `rbac_roles` (
  `role_id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `description` text NOT NULL,
  `importance` int(11) NOT NULL default '0',
  PRIMARY KEY  (`role_id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rbac_roles_has_domain_privileges`
--

CREATE TABLE `rbac_roles_has_domain_privileges` (
  `id` int(11) NOT NULL auto_increment,
  `role_id` int(11) default NULL,
  `privilege_id` int(10) unsigned NOT NULL default '0',
  `domain_id` int(10) unsigned NOT NULL default '0',
  `is_allowed` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_users_privileges_domains` (`role_id`,`domain_id`,`privilege_id`),
  KEY `fk_user_has_domain_privileges_domains` (`domain_id`),
  KEY `fk_user_has_domain_privileges_privileges` (`privilege_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rbac_users_has_roles`
--
		
CREATE TABLE `rbac_users_has_roles` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `role_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_users_roles` (`user_id`,`role_id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `rbac_users_has_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_main` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rbac_users_has_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `rbac_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `modules` (
  `id` int(11) NOT NULL auto_increment,
  `module_name` varchar(30) unique,
  `version` int(11) NOT NULL default 1,
  `installed_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `temp_members_db`
--

CREATE TABLE `temp_members_db` (
  `confirm_code` varchar(65) NOT NULL default '',
  `username` varchar(65) NOT NULL default '',
  `firstname` varchar(65) NOT NULL default '',
  `lastname` varchar(65) NOT NULL default '',
  `phone` varchar(65) NOT NULL default '',
  `email` varchar(65) NOT NULL default '',
  `hashed_password` varchar(200) NOT NULL,
  `interested_item` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

		
CREATE TABLE `files_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `file_id` int(11) default '0',
  `user_id` int(11) NOT NULL default '1',
  `user_ip` varchar(50) NOT NULL default '',
  `access_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`log_id`),
  KEY `file_id` (`file_id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user_main` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY (`file_id`) REFERENCES `files_main` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `files_main`
--

CREATE TABLE `files_main` (
  `file_id` int(11) NOT NULL auto_increment,
  `fileName` varchar(50) default NULL,
  `baseName` varchar(50) default NULL,
  `fileExt` varchar(4) default NULL,
  `mimeType` varchar(30) default NULL,
  `mimeName` varchar(50) default NULL,
  `filePath` varchar(100) default NULL,
  `fileDesc` varchar(200) default NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		
		
";


$stmt = $mysqli->multi_query($setup);

require_once("../admin/admin_include.php");
//create role admin, run the admin rbac

//require_once("../admin/admin_reset.php");

//create role admin, run the admin rbac


$ADMINUSER = "admin";
$ADMINPASS = "password";
$ADMINLASTNAME = "John";
$ADMINFIRSTNAME = "Smith";
$NULL = '';
// Bring in and initiate our db class
require_once("../../../../openMobas/classes/Database_Mysql.class.php");
$dbh = new DB_Mysql($DB_SETTINGS);

require_once("../../../../openMobas/classes/User_Session.class.php");
require_once("../../../../openMobas/classes/User.class.php");
require_once("../../../../openMobas/classes/Rbac_User.class.php");

$USER = new User($dbh, '', '', '');
$user_id = $USER->create_user("NULL", $ADMINUSER, $ADMINPASS, $ADMINLASTNAME, $ADMINFIRSTNAME, "kevin@pmsandiego.com", "");

//we created our admin, let the installer know it worked. 
echo "admin user created with id ".$user_id." <BR />";

$objSession = new User_Session($dbh);

//Not sure why those two lines were there
//$system = new Admin_System($dbh, '', '');
//$system->verify_rbac('admin');

//$RBAC = new Rbac($dbh, $USER, $rbac_user);
$RBAC = new Rbac($dbh, '', '', $objSession);
$RBAC->verify_rbac('admin');



$admin_role_id = $RBAC->create_roles("NULL", "admin", "System Wide Admin can do anything!", 100);
//get the all_actions id
$privilege_id = $RBAC->get_privilege_by_name("all_actions");
//get the all_objects id
$domain_id = $RBAC->get_domain_by_name("all_objects");
// give the roll the permission pair
$RBAC->create_permission("NULL", $admin_role_id, $privilege_id['privilege_id'], $domain_id['domain_id'], 1);
// give the user the role
$RBAC->create_users_mtm_roles("NULL", $user_id, $admin_role_id);

echo "it worked";



?>