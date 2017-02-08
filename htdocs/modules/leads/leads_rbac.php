<?php

$actions =  array();
$actions[] = "access_module";


$objects = array();
$objects[] = "leads_module";

--
-- Table structure for table `lead_category`
--

DROP TABLE IF EXISTS `lead_category`;
CREATE TABLE `lead_category` (
`leadcat_id` int(11) NOT NULL auto_increment,
`leadcat_name` varchar(40) NOT NULL default '',
PRIMARY KEY  (`leadcat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `lead_entry`
--

DROP TABLE IF EXISTS `lead_entry`;
CREATE TABLE `lead_entry` (
`leadentry_id` int(11) NOT NULL auto_increment,
`entry_txt` text,
`entry_date` datetime NOT NULL default '0000-00-00 00:00:00',
`user_id` int(11) NOT NULL default '1',
`lead_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`leadentry_id`),
KEY `lead_id` (`lead_id`),
KEY `user_id` (`user_id`),
CONSTRAINT `lead_entry_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `lead_main` (`lead_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `lead_entry_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_main` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `lead_main`
--

DROP TABLE IF EXISTS `lead_main`;
CREATE TABLE `lead_main` (
`lead_id` int(11) NOT NULL auto_increment,
`firstname` varchar(20) default NULL,
`lastname` varchar(30) default NULL,
`prop_street` varchar(40) NOT NULL default '',
`prop_city` varchar(20) NOT NULL default '',
`prop_state` char(2) NOT NULL default '',
`prop_zip` varchar(10) NOT NULL default '',
`comments` text,
`street` varchar(40) NOT NULL default '',
`city` varchar(20) NOT NULL default '',
`state` char(2) NOT NULL default '',
`zip` varchar(10) NOT NULL default '',
`phone_num` varchar(20) default '0-000-000-0000',
`phone_num2` varchar(20) default '0-000-000-0000',
`email_address` varchar(40) NOT NULL default '',
`whenopen_date` datetime NOT NULL default '0000-00-00 00:00:00',
`whenupdated_date` datetime NOT NULL default '0000-00-00 00:00:00',
`whenclosed_date` datetime NOT NULL default '0000-00-00 00:00:00',
`closed_yn` enum('NO','YES') NOT NULL default 'NO',
`whoopen_id` int(11) NOT NULL default '0',
`whoclose_id` int(11) NOT NULL default '0',
`whoupdated_id` int(11) NOT NULL default '0',
`whoassigned_id` int(11) NOT NULL default '0',
`converted_yn` enum('NO','YES') NOT NULL default 'NO',
`whenreturn_date` datetime NOT NULL default '0000-00-00 00:00:00',
`description` text NOT NULL,
  `leadorigin_id` int(11) default NULL,
  `prop_unit` varchar(20) NOT NULL default '',
  `color` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`lead_id`),
  KEY `whoclose_id` (`whoclose_id`),
  KEY `whoassigned_id` (`whoassigned_id`),
  KEY `whoupdated_id` (`whoupdated_id`),
  KEY `leadorigin_id` (`leadorigin_id`),
  CONSTRAINT `lead_main_ibfk_1` FOREIGN KEY (`leadorigin_id`) REFERENCES `lead_origin` (`leadorigin_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

  --
  -- Table structure for table `lead_module_settings`
  --

  DROP TABLE IF EXISTS `lead_module_settings`;
  CREATE TABLE `lead_module_settings` (
  `id` int(11) NOT NULL auto_increment,
  `setting_name` varchar(40) NOT NULL default '',
  `setting_value` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

  --
  -- Table structure for table `lead_origin`
  --

  DROP TABLE IF EXISTS `lead_origin`;
  CREATE TABLE `lead_origin` (
  `leadorigin_id` int(11) NOT NULL auto_increment,
  `leadorigin_name` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`leadorigin_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

  CREATE TABLE `lead_tags` (
  `leadcat_id` int(11),
  `lead_id` int(11),
  UNIQUE KEY `tagged`(`leadcat_id`, `lead_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  

  CREATE TABLE `lead_zipcodes` (
  `zip_id` int(11) NOT NULL auto_increment,
  `zip` varchar(10) NOT NULL unique,
  `zip_name` varchar(30) NOT NULL,
  PRIMARY KEY  (`zip_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  CREATE TABLE `lead_zip_mtm_user` (
  `zip_id` int(11),
  `user_id` int(11),
  UNIQUE KEY `zipToUser`(`user_id`, `zip_id`),
  FOREIGN KEY (`user_id`) REFERENCES  `user_main` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`zip_id`) REFERENCES `lead_zipcodes`(`zip_id`) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
?>
