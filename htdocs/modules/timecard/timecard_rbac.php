<?php

$actions =  array();
$actions[] = "access_module";
$actions[] = "clock_in";
$actions[] = "clock_out";
$actions[] = "edit_card";
$actions[] = "configure_module";


$objects = array();
$objects[] = "timecard_module";
$objects[] = "timecard";



CREATE TABLE `timecard_main` (
`timecard_id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL default '1',
`clock_in` timestamp NOT NULL default CURRENT_TIMESTAMP,
`clock_out` timestamp NULL default NULL,
PRIMARY KEY  (`timecard_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `timecard_edits` (
`timecardedit_id` int(11) NOT NULL auto_increment,
`user_id` int(11) NULL default '1',
`timecard_id` int(11) NOT NULL,
`oldclock_in` timestamp NOT NULL default '0000-00-00 00:00:00',
`newclock_in` timestamp NOT NULL default '0000-00-00 00:00:00',
`oldclock_out` timestamp NOT NULL default '0000-00-00 00:00:00',
`newclock_out` timestamp NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY  (`timecardedit_id`),
FOREIGN KEY (`timecard_id`) REFERENCES `timecard_main` (`timecard_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`user_id`) REFERENCES `user_main` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `timecard_module_settings` (
`id` int(11) NOT NULL auto_increment,
`setting_name` varchar(40) NOT NULL default '',
`setting_value` varchar(40) NOT NULL default '',
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `timecard_ips` (
`ip` decimal(39) unsigned default NULL unique
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


?>
