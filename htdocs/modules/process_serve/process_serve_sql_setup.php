<?php
/*
 *
 This file is part of OpenMobas
 Copyright (C) 2011, Kevin Milhoan

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


if($ACTION == "Install Module")
{
    echo "CREATING DATABASE TABLES FOR MODULE";
    $mysqli = new mysqli($DB_SETTINGS['dbhost'], $DB_SETTINGS['user'], $DB_SETTINGS['pass'], $DB_SETTINGS['dbname']);
    $stmt = $mysqli->multi_query($install_tables_string);
    //echo $install_tables_string;
}



?>
