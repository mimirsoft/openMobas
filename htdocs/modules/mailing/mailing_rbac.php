<?php

$actions =  array();
$actions[] = "access_module";


$objects = array();
$objects[] = "mailing_module";

--
-- Table structure for table `mailing_stored`
--

DROP TABLE IF EXISTS `mailing_stored`;
CREATE TABLE `mailing_stored` (
`mailing_id` int(11) NOT NULL auto_increment,
`mailing_name` varchar(80) NOT NULL default '',
`mailing_body` text,
PRIMARY KEY  (`mailing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


?>
