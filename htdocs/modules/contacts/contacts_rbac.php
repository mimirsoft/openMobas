<?php

$actions =  array();
$actions[] = "access_module";
$actions[] = "add_contact";
$actions[] = "delete_contact";
$actions[] = "edit_contact";


$objects = array();
$objects[] = "contacts_module";
$objects[] = "all_contacts";
$objects[] = "own_contacts";

$install_tables_string = "
DROP TABLE IF EXISTS `contacts_address`;
CREATE TABLE `contacts_address` (
`address_id` int(11) NOT NULL auto_increment,
`street` varchar(40) NOT NULL default '',
`city` varchar(20) NOT NULL default '',
`state` char(2) NOT NULL default '',
`zip` varchar(10) NOT NULL default '',
`addresstype_id` int(3) NOT NULL default '0',
`contacts_id` int(11) NOT NULL default '0',
`careof` varchar(40) NOT NULL default '',
PRIMARY KEY  (`address_id`),
KEY `contacts_id` (`contacts_id`),
KEY `addresstype_id` (`addresstype_id`),
CONSTRAINT `contacts_address_ibfk_1` FOREIGN KEY (`contacts_id`) REFERENCES `contacts_main` (`contacts_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `contacts_address_ibfk_2` FOREIGN KEY (`addresstype_id`) REFERENCES `contacts_addresstype` (`addresstype_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



DROP TABLE IF EXISTS `contacts_addresstype`;
CREATE TABLE `contacts_addresstype` (
`addresstype_id` int(3) NOT NULL auto_increment,
`addresstype_name` varchar(20) NOT NULL default '',
PRIMARY KEY  (`addresstype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `contacts_email`;
CREATE TABLE `contacts_email` (
`email_id` int(11) NOT NULL auto_increment,
`email_address` varchar(40) NOT NULL default '',
`contacts_id` int(11) NOT NULL default '0',
`emailtype_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`email_id`),
KEY `contacts_id` (`contacts_id`),
KEY `emailtype_id` (`emailtype_id`),
CONSTRAINT `contacts_email_ibfk_1` FOREIGN KEY (`contacts_id`) REFERENCES `contacts_main` (`contacts_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `contacts_email_ibfk_2` FOREIGN KEY (`emailtype_id`) REFERENCES `contacts_emailtype` (`emailtype_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



DROP TABLE IF EXISTS `contacts_emailtype`;
CREATE TABLE `contacts_emailtype` (
`emailtype_id` int(11) NOT NULL auto_increment,
`emailtype_name` varchar(20) NOT NULL default '',
PRIMARY KEY  (`emailtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO contacts_emailtype (emailtype_id, emailtype_name )
VALUES (1, 'BUSINESS' );
INSERT INTO contacts_emailtype ( emailtype_id, emailtype_name )
VALUES (2, 'PERSONAL' );

DROP TABLE IF EXISTS `contacts_activity`;
CREATE TABLE `contacts_activity` (
`contact_activity_id` int(11) NOT NULL auto_increment,
`contact_activity_title` varchar(40) NOT NULL default '',
`contact_activity_txt` text,
`contact_activity_date` datetime NOT NULL default '0000-00-00 00:00:00',
`user_id` int(11) NOT NULL default '1',
  contacts_id int(11) NOT NULL default '1',
  PRIMARY KEY  (`contact_activity_id`),
  KEY `user_id` (`user_id`),
  KEY contacts_id (contacts_id),
  FOREIGN KEY (`user_id`) REFERENCES `user_main` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (contacts_id) REFERENCES `contacts_main` (contacts_id) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  --
  -- Table structure for table `contacts_main`
  --


  DROP TABLE IF EXISTS `contacts_main`;
  CREATE TABLE `contacts_main` (
  `contacts_id` int(11) NOT NULL auto_increment,
  `lastname` varchar(30) default NULL,
  `firstname` varchar(20) default NULL,
  `ssn` varchar(11) default '000-00-0000',
  `contacttype_id` int(11) NOT NULL default '0',
  `contact_notes` text NOT NULL,
  		PRIMARY KEY  (`contacts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `contacts_phone`;
CREATE TABLE `contacts_phone` (
`phone_id` int(11) NOT NULL auto_increment,
`phone_num` varchar(20) default '0-000-000-0000',
`phonetype_id` int(3) NOT NULL default '0',
`contacts_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`phone_id`),
KEY `contacts_id` (`contacts_id`),
KEY `phonetype_id` (`phonetype_id`),
CONSTRAINT `contacts_phone_ibfk_1` FOREIGN KEY (`contacts_id`) REFERENCES `contacts_main` (`contacts_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `contacts_phone_ibfk_2` FOREIGN KEY (`phonetype_id`) REFERENCES `contacts_phonetype` (`phonetype_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `contacts_phonetype`;
CREATE TABLE `contacts_phonetype` (
`phonetype_id` int(3) NOT NULL auto_increment,
`phonetype_name` varchar(20) NOT NULL default '',
PRIMARY KEY  (`phonetype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `contacts_type`;
CREATE TABLE `contacts_type` (
`contacttype_id` int(11) NOT NULL auto_increment,
`contacttype_name` varchar(50) NOT NULL default '',
PRIMARY KEY  (`contacttype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO contacts_type ( contacttype_id, contacttype_name )
VALUES (1, 'CUSTOMER' );
INSERT INTO contacts_type ( contacttype_id, contacttype_name )
VALUES (2, 'VENDOR' );
INSERT INTO contacts_type ( contacttype_id, contacttype_name )
VALUES (3, 'TENANT' );
INSERT INTO contacts_type ( contacttype_id, contacttype_name )
VALUES (4, 'OWNER' );



CREATE TABLE `contact_tags` (
`contacttype_id` int(11),
`contacts_id` int(11),
UNIQUE KEY `tagged`(`contacttype_id`, `contacts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";



?>
