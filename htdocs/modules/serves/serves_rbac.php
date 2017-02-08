<?php

$actions =  array();
$actions[] = "access_module";


$objects = array();
$objects[] = "process_serve_module";

$install_tables_string =

"CREATE TABLE `process_serve_main` (
`serve_id` int(11) NOT NULL auto_increment,
`firstname` varchar(20) default NULL,
`lastname` varchar(30) default NULL,
`comments` text,
`streetnumber` varchar(20) NOT NULL default '',
`street` varchar(60) NOT NULL default '',
`city` varchar(20) NOT NULL default '',
`state` char(2) NOT NULL default '',
`zip` varchar(10) NOT NULL default '',
`type` enum('UD','SC','FL','TRO','CH','DV') NOT NULL default 'SC',
`date` datetime NOT NULL default '0000-00-00 00:00:00',
`complete` tinyint(1) NOT NULL default 0,
PRIMARY KEY  (`serve_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$mysqli = new mysqli($DB_SETTINGS['dbhost'], $DB_SETTINGS['user'], $DB_SETTINGS['pass'], $DB_SETTINGS['dbname']);

$stmt = $mysqli->multi_query($install_tables_string);


?>
