<?php

$actions =  array();
$actions[] = "access_module";
$actions[] = "view_statement";
$actions[] = "configure_statement";
$actions[] = "address_statement";


$objects = array();
$objects[] = "statements_module";
$objects[] = "own_accounts";
$objects[] = "all_accounts";
$objects[] = "public_statements";
$objects[] = "private_statements";
$objects[] = "all_statements";


-- Table structure for table `statements_budget`
--

DROP TABLE IF EXISTS `statements_budgetlines`;
CREATE TABLE `statements_budgetlines` (
`budgetline_id` int(11) NOT NULL auto_increment,
`budget_account` int(11) default '0',
`budget_monthly` decimal(11,2) default '0.00',
`budget_yearly` decimal(13,2) default '0.00',
`budget_id` int(11) NOT NULL,
PRIMARY KEY  (`budgetline_id`),
KEY `budget_account` (`budget_account`),
KEY `budget_id` (`budget_id`),
CONSTRAINT `statements_budgetlines_ibfk_1` FOREIGN KEY (`budget_account`) REFERENCES `transactions_accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `statements_budgetlines_ibfk_2` FOREIGN KEY (`budget_id`) REFERENCES `statements_budget` (`budget_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `statements_budget`;
CREATE TABLE `statements_budget` (
`budget_id` int(11) NOT NULL auto_increment,
`budget_name` varchar(40) NOT NULL default '',
`start_date` date default NULL,
`end_date` date default NULL,
PRIMARY KEY  (`budget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `statements_main`
--

DROP TABLE IF EXISTS `statements_main`;
CREATE TABLE `statements_main` (
`statement_id` int(11) NOT NULL auto_increment,
`statement_name` varchar(30) NOT NULL default '',
`statement_array` text NOT NULL,
`statement_privilege` enum('PUBLIC','PRIVATE') NOT NULL default 'PRIVATE',
PRIMARY KEY  (`statement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

?>
