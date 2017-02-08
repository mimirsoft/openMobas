<?php

$actions =  array();
$actions[] = "access_module";


$objects = array();
$objects[] = "maintenance_module";


DROP TABLE IF EXISTS `maintenance_main`;
CREATE TABLE `maintenance_main` (
`maintenance_id` int(11) NOT NULL auto_increment,
`maintenance_title` varchar(200) NOT NULL default '',
`maintenance_txt` text,
`closed_yn` enum('NO','YES') NOT NULL default 'NO',
`whoopen_id` int(11) NOT NULL default '0',
`whoclosed_id` int(11) NOT NULL default '0',
`whenopen_date` datetime NOT NULL default '0000-00-00 00:00:00',
`whenclosed_date` datetime NOT NULL default '0000-00-00 00:00:00',
`property_id` int(11) default '0',
`inventory_id` int(11) default '0',
`customer_id` int(11) default NULL,
`whoopen_username` varchar(60) default '',
`whoclosed_username` varchar(60) default '',
`action_needed` tinyint(1) NOT NULL default 0,
`whenfollowup` timestamp NOT NULL default '0000-00-00 00:00:00',
`whenupdate` timestamp NOT NULL default NOW(),
PRIMARY KEY  (`maintenance_id`),
FOREIGN KEY (`whoopen_id`) REFERENCES `user_main` (`user_id`),
FOREIGN KEY (`property_id`) REFERENCES `properties_main` (`property_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`inventory_id`) REFERENCES `inventory_items` (`inventory_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`customer_id`) REFERENCES `cv_main`(`cv_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
--
-- Table structure for table `maintenance_cases`
--

DROP TABLE IF EXISTS `maintenance_cases`;
CREATE TABLE `maintenance_cases` (
`maintenance_case_id` int(11) NOT NULL auto_increment,
`maintenance_id` int(11) default '0',
`case_id` int(11) default '0',
PRIMARY KEY  (`maintenance_case_id`),
KEY `maintenance_id` (`maintenance_id`),
CONSTRAINT `maintenance_cases_ibfk_1` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance_main` (`maintenance_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `maintenance_mtm_contacts`;
CREATE TABLE `maintenance_mtm_contacts` (
`contacts_id` int(11),
`maintenance_id` int(11),
FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance_main` (`maintenance_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (contacts_id) REFERENCES `contacts_main` (contacts_id) ON DELETE CASCADE ON UPDATE CASCADE,
UNIQUE KEY `tagged`(`maintenance_id`, `contacts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `maintenance_mtm_cv_main` (
`cv_id` int(11),
`maintenance_id` int(11),
FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance_main` (`maintenance_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (cv_id) REFERENCES `cv_main` (cv_id) ON DELETE CASCADE ON UPDATE CASCADE,
UNIQUE KEY `tagged`(`maintenance_id`, `cv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
?>
