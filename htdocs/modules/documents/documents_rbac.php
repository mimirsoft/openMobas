<?php

$actions =  array();
$actions[] = "access_module";
$actions[] = "upload_documents";
$actions[] = "access_documents";
$actions[] = "delete_documents";
$actions[] = "edit_documents";


$objects = array();
$objects[] = "documents_module";
$objects[] = "all_documents";
$objects[] = "own_documents";
$objects[] = "public_documents";


--
-- Table structure for table `documents_categories`
--

DROP TABLE IF EXISTS `documents_categories`;
CREATE TABLE `documents_categories` (
`category_id` int(11) NOT NULL auto_increment,
`category_name` varchar(50) default NULL,
`category_priority` int(11) not NULL default '0',
`is_active` int(11) not NULL default '1',
PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS `documents_main`;
CREATE TABLE `documents_main` (
`document_id` int(11) NOT NULL auto_increment,
`document_date` date default NULL,
`document_name` varchar(80) default NULL,
`file_id` int(11) default '0',
`document_security` enum('PUBLIC','PRIVATE','PRIVILEGED','SECRET') NOT NULL default 'PUBLIC',
PRIMARY KEY  (`document_id`),
KEY `file_id` (`file_id`),
CONSTRAINT `documents_main_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files_main` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `documents_multi`
--

DROP TABLE IF EXISTS `documents_multi`;
CREATE TABLE `documents_multi` (
`document_mtm_category` int(11) NOT NULL auto_increment,
`category_id` int(11) default '0',
`document_id` int(11) default '0',
PRIMARY KEY  (`document_mtm_category`),
KEY `document_id` (`document_id`),
KEY `category_id` (`category_id`),
CONSTRAINT `documents_multi_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents_main` (`document_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `documents_multi_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `documents_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


?>
