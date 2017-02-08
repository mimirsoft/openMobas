<?php

$actions =  array();
$actions[] = "access_module";
$actions[] = "create_case";
$actions[] = "update_case";
$actions[] = "delete_case";
$actions[] = "close_case";


$objects = array();
$objects[] = "casesystem_module";
$objects[] = "own_cases";
$objects[] = "all_cases";



--
-- Table structure for table `casesystem_entry`
--

DROP TABLE IF EXISTS `casesystem_entry`;
CREATE TABLE `casesystem_entry` (
`caseentry_id` int(11) NOT NULL auto_increment,
`casesystem_txt` text,
`hiddencasesystem_txt` text,
`caseentry_date` datetime NOT NULL default '0000-00-00 00:00:00',
`user_id` int(11) NOT NULL default '1',
`case_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`caseentry_id`),
KEY `case_id` (`case_id`),
KEY `user_id` (`user_id`),
CONSTRAINT `casesystem_entry_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `casesystem_main` (`case_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `casesystem_entry_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_main` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `casesystem_main`
--

DROP TABLE IF EXISTS `casesystem_main`;
CREATE TABLE `casesystem_main` (
`case_id` int(11) NOT NULL auto_increment,
`whenopen_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `whenupdated_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `whenclosed_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `whenfollowup` timestamp NOT NULL default '0000-00-00 00:00:00',
  `closed_yn` enum('NO','YES') NOT NULL default 'NO',
  `whoopen_id` int(11) NOT NULL default '1',
  `whoclose_id` int(11) NOT NULL default '1',
  `whoupdated_id` int(11) NOT NULL default '1',
  `whoassigned_id` int(11) NOT NULL default '1',
  `case_title` varchar(40) NOT NULL default '',
  `action_needed` tinyint(1) NOT NULL default 0,
  `status_text` text,
  PRIMARY KEY  (`case_id`),
  KEY `whoclose_id` (`whoclose_id`),
  KEY `whoassigned_id` (`whoassigned_id`),
  KEY `whoupdated_id` (`whoupdated_id`),
  CONSTRAINT `casesystem_main_ibfk_1` FOREIGN KEY (`whoclose_id`) REFERENCES `user_main` (`user_id`),
  CONSTRAINT `casesystem_main_ibfk_2` FOREIGN KEY (`whoassigned_id`) REFERENCES `user_main` (`user_id`),
  CONSTRAINT `casesystem_main_ibfk_3` FOREIGN KEY (`whoupdated_id`) REFERENCES `user_main` (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  CREATE TABLE `cases_mtm_cv_id` (
  `case_id` int(11),
  `cv_id` int(11),
  UNIQUE KEY `tagged`(`case_id`, `cv_id`),
  FOREIGN KEY (case_id) REFERENCES casesystem_main(case_id) ON DELETE CASCADE,
  FOREIGN KEY (cv_id) REFERENCES cv_main(cv_id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
?>
