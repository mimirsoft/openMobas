<?php

$actions =  array();
$actions[] = "access_module";
$actions[] = "manage_inventory";


$objects = array();
$objects[] = "cv_master_module";
$objects[] = "inventory_items";

CREATE TABLE `cv_main` (
`cv_id` int(11) NOT NULL auto_increment,
`cv_name` varchar(80) NOT NULL default '',
`cv_default_careof` varchar(80) NOT NULL default '',
`cv_default_address` varchar(60) NOT NULL default '',
`cv_default_city` varchar(60) NOT NULL default '',
`cv_default_state` varchar(60) NOT NULL default '',
`cv_default_zip` varchar(60) NOT NULL default '',
`cv_number` varchar(40) NOT NULL default '',
`cv_default_email` varchar(80) NOT NULL default '',
`cv_default_phone` varchar(20) NOT NULL default '',
`cv_default_statement_type` varchar(40) NOT NULL default 'PRINT',
`cv_default_payment_type` varchar(40) NOT NULL default 'CHECK',
`cv_default_invoice_type` varchar(40) NOT NULL default 'PRINT',
`tax_id` varchar(40) NOT NULL default '',
`is_vendor` tinyint(1) default 0,
`is_customer` tinyint(1) default 0,
`orders_accepted` tinyint(1) default 0,
`payments_accepted` tinyint(1) default 0,
`purchases_allowed` tinyint(1) default 0,
`disbursements_allowed` tinyint(1) default 0,
`clear_with_customer` tinyint(1) default 0,
`clear_with_vendor` tinyint(1) default 0,
`current` tinyint(1) NOT NULL default 1,
`lastupdated` datetime NOT NULL default '0000-00-00 00:00:00',
`update_email_sent` datetime NOT NULL default '0000-00-00 00:00:00',
`accounts_balance_total` decimal(11,2) default '0.00',
`receivable_invoices_total` decimal(11,2) default '0.00',
`credit_limit` decimal(11,2) default '0.00',
`gl_account_receivable`  int(11) NULL,
`gl_account_payable`  int(11) NULL,
`cv_notes`  text NULL,
`vendor_invoices`   smallint(1) default 0,
`is_active` tinyint(1) default 1,
`is_closed` tinyint(1) default 0,
`payments_accepted_note` varchar(200) default '',
PRIMARY KEY  (`cv_id`),
FOREIGN KEY (`gl_account_receivable`) REFERENCES `transactions_accounts` (`account_id`),
FOREIGN KEY (`gl_account_payable`) REFERENCES `transactions_accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cv_contacts` (
`cv_contacts_id` int(11) NOT NULL auto_increment,
`cv_id` int(11) default '0',
`contacts_id` int(11) default '0',
PRIMARY KEY  (`cv_contacts_id`),
KEY `contacts_id` (`contacts_id`),
FOREIGN KEY (`contacts_id`) REFERENCES `contacts_main` (`contacts_id`),
FOREIGN KEY (`cv_id`) REFERENCES `cv_main` (`cv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `cv_tags`;

DROP TABLE IF EXISTS `cv_category`;
CREATE TABLE `cv_category` (
`cv_category_id` int(11) NOT NULL auto_increment,
`cv_category_name` varchar(40) NOT NULL unique,
PRIMARY KEY  (`cv_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `cv_tags` (
`cv_category_id` int(11),
`cv_id` int(11),
UNIQUE KEY `tagged`(`cv_category_id`, `cv_id`),
FOREIGN KEY (cv_category_id) REFERENCES cv_category(cv_category_id) ON DELETE CASCADE,
FOREIGN KEY (cv_id) REFERENCES cv_main(cv_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `cv_settings` (
`default_account_receivable` int(11) default '0',
`default_account_payable` int(11) default '0',
KEY `default_account_receivable` (`default_account_receivable`),
FOREIGN KEY (`default_account_receivable`) REFERENCES `transactions_accounts` (`account_id`),
KEY `default_account_payable` (`default_account_payable`),
FOREIGN KEY (`default_account_payable`) REFERENCES `transactions_accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



DROP TABLE IF EXISTS `cv_mtm_users`;
CREATE TABLE `cv_mtm_users` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL default '0',
`cv_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `user_id` (`user_id`),
KEY `cv_id` (`cv_id`),
FOREIGN KEY (`user_id`) REFERENCES `user_main` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cv_id`) REFERENCES `cv_main` (`cv_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


?>
