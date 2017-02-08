<?php

$actions =  array();
$actions[] = "access_module";
$actions[] = "configure_module";


$objects = array();
$objects[] = "properties_module";

--
-- Table structure for table `properties_comments`
--

DROP TABLE IF EXISTS `properties_comments`;
CREATE TABLE `properties_comments` (
`propertycomment_id` int(11) NOT NULL auto_increment,
`propertycomment_txt` text,
`property_id` int(11) default '0',
PRIMARY KEY  (`propertycomment_id`),
KEY `property_id` (`property_id`),
CONSTRAINT `properties_comments_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties_main` (`property_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `properties_files`
--

DROP TABLE IF EXISTS `properties_files`;
CREATE TABLE `properties_files` (
`prop_to_file_id` int(11) NOT NULL auto_increment,
`file_id` int(11) default '0',
`property_id` int(11) default '0',
PRIMARY KEY  (`prop_to_file_id`),
KEY `file_id` (`file_id`),
KEY `property_id` (`property_id`),
CONSTRAINT `properties_files_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files_main` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `properties_files_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties_main` (`property_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `properties_main`
--

DROP TABLE IF EXISTS `properties_main`;
CREATE TABLE `properties_main` (
`property_id` int(11) NOT NULL auto_increment,
`property_address` varchar(30) default '',
`property_aptnum` varchar(5) default '',
`property_propnum` int(5) default '0',
`property_city` varchar(20) default '',
`property_state` char(2) default '',
`property_zip` varchar(11) default '',
`property_county` varchar(20) default '',
`property_thomasguidenum` varchar(6) default '',
`property_complex` varchar(20) default '',
`property_area` varchar(20) default '',
`property_parknum` varchar(30) NOT NULL default '',
`property_mailboxnum` varchar(5) default '',
`property_sqft` int(5) default '0',
`property_yrblt` int(4) default '0',
`property_numrm` tinyint(1) default '0',
`property_numbed` tinyint(1) default '0',
`property_numbath` varchar(7) default '0',
`property_refridge` varchar(30) NOT NULL default '',
`property_dishwasher`  varchar(30) NOT NULL default '',
`property_stove` varchar(30) NOT NULL default '',
`property_stovetype` varchar(30) NOT NULL default '',
`property_microwave` varchar(30) NOT NULL default '',
`property_fireplace`  varchar(30) NOT NULL default '',
`property_air`  varchar(30) NOT NULL default '',
`property_heat`  varchar(30) NOT NULL default '',
`property_washer` varchar(40) NOT NULL default '',
`property_dryer` varchar(40) NOT NULL default '',
`property_parking` varchar(40) NOT NULL default '',
`property_garage` varchar(30) NOT NULL default '',
`property_garagenum` varchar(30) NOT NULL default '',
`property_garageinfo` varchar(100) NOT NULL default '',
`property_openers` tinyint(1) NOT NULL default '0',
`property_pool` varchar(30) NOT NULL default '',
`property_spa` varchar(30) NOT NULL default '',
`property_landscaping` varchar(30) NOT NULL default '',
`property_pets` varchar(30) NOT NULL default '',
`property_petdeposit` decimal(5,2) NOT NULL default '0.00',
`property_petdescription` varchar(100) default '0',
`property_smoking` enum('Y','N') NOT NULL default 'N',
`property_whopaygas` varchar(30) NOT NULL default 'TENANT',
`property_whopayelec` varchar(30) NOT NULL default 'TENANT',
`property_whopaywater` varchar(30) NOT NULL default 'TENANT',
`property_whopaygarbage` varchar(30) NOT NULL default 'TENANT',
`property_hoa` varchar(50) NOT NULL default '',
`property_hoa_number` varchar(20) NOT NULL default '',
`property_hoa_manager` varchar(50) NOT NULL default '',
`property_rentdesired` decimal(8,2) NOT NULL default '0.00',
`property_rentmin` decimal(8,2) NOT NULL default '0.00',
`property_leasedesired` int(2) NOT NULL default '0',
`property_leasemin` int(2) NOT NULL default '0',
`property_dateavail` date NOT NULL default '0000-00-00',
`propertytype_id` int(11) default '0',
`property_feepercent` decimal(3,2) NOT NULL default '0.00',
`property_status` enum('CURRENT','CLOSED','OLD') NOT NULL default 'CURRENT',
`property_avail` enum('Y','N') NOT NULL default 'N',
`property_sale` enum('Y','N') NOT NULL default 'N',
`property_maintenance` enum('Y','N') NOT NULL default 'Y',
`property_shortterm` enum('Y','N') NOT NULL default 'N',
`property_comments` text NOT NULL,
`property_description` text NOT NULL,
PRIMARY KEY  (`property_id`),
		KEY `propertytype_id` (`propertytype_id`),
		CONSTRAINT `properties_main_ibfk_1` FOREIGN KEY (`propertytype_id`) REFERENCES `properties_type` (`propertytype_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

		--
		-- Table structure for table `properties_module_settings`
		--

		DROP TABLE IF EXISTS `properties_module_settings`;
		CREATE TABLE `properties_module_settings` (
		`id` int(11) NOT NULL auto_increment,
		`setting_name` varchar(30) NOT NULL default '',
		`setting_value` varchar(70) NOT NULL default '',
		PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

		--
-- Table structure for table `properties_type`
--

DROP TABLE IF EXISTS `properties_type`;
CREATE TABLE `properties_type` (
		`propertytype_id` int(11) NOT NULL auto_increment,
				`propertytype_name` varchar(20) NOT NULL default '',
				PRIMARY KEY  (`propertytype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO properties_type (propertytype_id, propertytype_name)
VALUES (1, 'Condo' );
INSERT INTO properties_type (propertytype_id, propertytype_name)
VALUES (2, 'Single Family Home' );

--
-- Table structure for table `property_owners`
--

DROP TABLE IF EXISTS `property_owners`;
CREATE TABLE `property_owners` (
		`property_owner_id` int(11) NOT NULL auto_increment,
		`property_id` int(11) default '0',
		`contacts_id` int(11) default '0',
		`ownership_percentage` decimal(3,2) NOT NULL default '0.00',
		PRIMARY KEY  (`property_owner_id`),
		KEY `property_id` (`property_id`),
		KEY `contacts_id` (`contacts_id`),
		CONSTRAINT `property_owners_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties_main` (`property_id`),
		CONSTRAINT `property_owners_ibfk_2` FOREIGN KEY (`contacts_id`) REFERENCES `contacts_main` (`contacts_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

?>
