<?php

$actions =  array();
$actions[] = "access_module";
$actions[] = "access_invoices";


$objects = array();
$objects[] = "invoices_module";
$objects[] = "all_invoices";
$objects[] = "my_invoices";


--
-- Table structure for table `recurring_check`
--

DROP TABLE IF EXISTS `recurring_check`;
CREATE TABLE `recurring_check` (
`recurringcheck_id` int(11) NOT NULL auto_increment,
`recurringcheck_type` enum('UNIQUE','CONTACT') NOT NULL default 'CONTACT',
`recurring_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`recurringcheck_id`),
UNIQUE KEY `recurring_id` (`recurring_id`),
CONSTRAINT `recurring_check_ibfk_1` FOREIGN KEY (`recurring_id`) REFERENCES `recurring_main` (`recurring_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `recurring_checkcontact`
--

DROP TABLE IF EXISTS `recurring_checkcontact`;
CREATE TABLE `recurring_checkcontact` (
`checkcontact_id` int(11) NOT NULL auto_increment,
`check_memo` varchar(40) NOT NULL default '',
`address_id` int(11) NOT NULL default '0',
`recurringcheck_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`checkcontact_id`),
UNIQUE KEY `recurringcheck_id` (`recurringcheck_id`),
KEY `address_id` (`address_id`),
CONSTRAINT `recurring_checkcontact_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `contacts_address` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `recurring_checkcontact_ibfk_2` FOREIGN KEY (`recurringcheck_id`) REFERENCES `recurring_check` (`recurringcheck_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `recurring_checkunique`
--
DROP TABLE IF EXISTS `recurring_debit_credit`;
CREATE TABLE `recurring_debit_credit` (
`recurring_dc_id` int(11) NOT NULL auto_increment,
`recurring_account` int(11) default '0',
`recurring_id` int(11) default '0',
`recurring_dc_amount` decimal(11,2) default '0.00',
`recurring_dc` enum('DEBIT','CREDIT') NOT NULL default 'DEBIT',
PRIMARY KEY  (`recurring_dc_id`),
KEY `recurring_id` (`recurring_id`),
KEY `recurring_account` (`recurring_account`),
FOREIGN KEY (`recurring_id`) REFERENCES `recurring_main` (`recurring_id`) ON DELETE CASCADE,
FOREIGN KEY (`recurring_account`) REFERENCES `transactions_accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



DROP TABLE IF EXISTS `recurring_checkunique`;
CREATE TABLE `recurring_checkunique` (
`checkunique_id` int(11) NOT NULL auto_increment,
`checkunique_name` varchar(40) NOT NULL default '',
`checkunique_memo` varchar(40) NOT NULL default '',
`checkunique_street` varchar(40) NOT NULL default '',
`checkunique_city` varchar(20) NOT NULL default '',
`checkunique_state` char(2) NOT NULL default '',
`checkunique_zip` varchar(10) NOT NULL default '',
`checkunique_careof` varchar(80) NOT NULL default '',
`recurringcheck_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`checkunique_id`),
UNIQUE KEY `recurringcheck_id` (`recurringcheck_id`),
CONSTRAINT `recurring_checkunique_ibfk_1` FOREIGN KEY (`recurringcheck_id`) REFERENCES `recurring_check` (`recurringcheck_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `recurring_main`
--

DROP TABLE IF EXISTS `recurring_main`;
CREATE TABLE `recurring_main` (
`recurring_id` int(11) NOT NULL auto_increment,
`recurring_comment` varchar(70) default '',
`recurring_amount` decimal(7,2) default '0.00',
`recurringtype_id` int(11) NOT NULL default '0',
  `statement_type` enum('EMAIL','PRINT') NOT NULL default 'PRINT',
  PRIMARY KEY  (`recurring_id`),
  KEY `recurringtype_id` (`recurringtype_id`),
  CONSTRAINT `recurring_main_ibfk_3` FOREIGN KEY (`recurringtype_id`) REFERENCES `recurring_type` (`recurringtype_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

  --
  -- Table structure for table `recurring_type`
  --

  DROP TABLE IF EXISTS `recurring_type`;
  CREATE TABLE `recurring_type` (
  `recurringtype_id` int(11) NOT NULL auto_increment,
  `recurringtype_name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`recurringtype_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;



  CREATE TABLE `inventory_types` (
  `inventorytype_id` int(11) NOT NULL auto_increment,
  `inventorytype_name` varchar(60) default '',
  `service_policy` varchar(30) default '',
  `onsale_generate_po` smallint(1) default 0,
  `nativetable_name` varchar(60) default '',
  `po_expense_account` int(11) NULL,
  `inventory_account` int(11) NULL,
  `sales_revenue_account` int(11) NULL,
  `non_clearing_item` smallint(1) default 0,
  PRIMARY KEY  (`inventorytype_id`),
  FOREIGN KEY (`po_expense_account`) REFERENCES `transactions_accounts` (`account_id`) ,
  FOREIGN KEY (`inventory_account`) REFERENCES `transactions_accounts` (`account_id`) ,
  FOREIGN KEY (`sales_revenue_account`) REFERENCES `transactions_accounts` (`account_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  CREATE TABLE `inventory_items` (
  `inventory_id` int(11) NOT NULL auto_increment,
  `cv_id` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `item_name` varchar(250) default '',
  `item_description` varchar(250) default '',
  `retail_price` decimal(11,2) default '0.00',
  `wholesale_price` decimal(11,2) default '0.00',
  `item_manager` int(11) NOT NULL default 1,
  `available` tinyint(1) NOT NULL default 1,
  `current` tinyint(1) NOT NULL default 1,
  `instock` tinyint(1) NOT NULL default 1,
  `availabledate`  date NOT NULL default '0000-00-00',
  `service_policy` varchar(30) default '',
  `external_link` varchar(100) default '',
  `on_sale_auto_purchase` smallint(1) default 0,
  `email_vendor_on_purchase` smallint(1) default 0,
  `item_notes`  text NULL,
  PRIMARY KEY  (`inventory_id`),
  FOREIGN KEY (`cv_id`) REFERENCES `cv_main` (`cv_id`),
  FOREIGN KEY (`item_manager`) REFERENCES `user_main` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`item_type`) REFERENCES `inventory_types` (`inventorytype_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
  
  
  CREATE TABLE `inventory_inquiries` (
  `inquiry_id` int(11) NOT NULL auto_increment,
  `inventory_id` int(11) NOT NULL,
  `inquirer_firstname` varchar(50) default '',
  `inquirer_lastname` varchar(50) default '',
  `inquirer_phone` varchar(50) default '',
  `inquirer_phone2` varchar(50) default '',
  `inquirer_email` varchar(50) default '',
  `inquiry_type` enum('GENERAL','APPOINTMENT') NOT NULL default 'GENERAL',
  `notes` varchar(200) default '',
  `inquiry_date`   datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`inquiry_id`),
  FOREIGN KEY (`inventory_id`) REFERENCES `inventory_items` (`inventory_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
  CREATE TABLE `invoices_main` (
  `invoice_id` int(11) NOT NULL auto_increment,
  `invoice_clientreference` varchar(60) NOT NULL default '',
  `invoice_pretaxtotal` decimal(11,2) default '0.00',
  `invoice_tax` decimal(11,2) default '0.00',
  `invoice_total` decimal(11,2) default '0.00',
  `date_charged` date default NULL,
  `total_remitted` decimal(11,2) default '0.00',
  `paid_in_full` int(1) NOT NULL default 0,
  `billto_name` varchar(60) NOT NULL default '',
  `billto_address1` varchar(60) NOT NULL default '',
  `billto_address2` varchar(60) NOT NULL default '',
  `billto_city` varchar(25) NOT NULL default '',
  `billto_state` varchar(20) NOT NULL default '',
  `billto_zip` varchar(15) NOT NULL default '',
  `shipto_name` varchar(60) NOT NULL default '',
  `shipto_address1` varchar(60) NOT NULL default '',
  `shipto_address2` varchar(60) NOT NULL default '',
  `shipto_city` varchar(25) NOT NULL default '',
  `shipto_state` varchar(20) NOT NULL default '',
  `shipto_zip` varchar(15) NOT NULL default '',
  `customer_id` int(11) NULL,
  `customer_account_id` int(11) NULL,
  `revenue_account_id` int(11) NULL,
  `auto_purchases_complete` smallint(1) NOT NULL default 0,
  `invoice_gl_entry` int(11) default NULL UNIQUE,
  `invoice_description` text,
  PRIMARY KEY  (`invoice_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `cv_main` (`cv_id`) ,
  FOREIGN KEY (`customer_account_id`) REFERENCES `transactions_accounts` (`account_id`) ,
  FOREIGN KEY (`revenue_account_id`) REFERENCES `transactions_accounts` (`account_id`) ,
  FOREIGN KEY (`invoice_gl_entry`) REFERENCES `transactions_main` (`transaction_id`)  ON DELETE SET NULL
  )ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  CREATE TABLE `invoices_recurring` (
  `recurringinvoice_id` int(11) NOT NULL auto_increment,
  `invoice_clientreference` varchar(60) NOT NULL default '',
  `lastcharged` date default NULL,
  `frequency` int(11) NOT NULL default 30,
  `times_charged` int(11) NOT NULL default 0,
  `invoice_pretaxtotal` decimal(11,2) default '0.00',
  `invoice_tax` decimal(11,2) default '0.00',
  `invoice_total` decimal(11,2) default '0.00',
  `billto_name` varchar(60) NOT NULL default '',
  `billto_address1` varchar(60) NOT NULL default '',
  `billto_address2` varchar(60) NOT NULL default '',
  `billto_city` varchar(25) NOT NULL default '',
  `billto_state` varchar(20) NOT NULL default '',
  `billto_zip` varchar(15) NOT NULL default '',
  `shipto_name` varchar(60) NOT NULL default '',
  `shipto_address1` varchar(60) NOT NULL default '',
  `shipto_address2` varchar(60) NOT NULL default '',
  `shipto_city` varchar(25) NOT NULL default '',
  `shipto_state` varchar(20) NOT NULL default '',
  `shipto_zip` varchar(15) NOT NULL default '',
  `customer_id` int(11) NULL,
  `customer_account_id` int(11) NULL,
  `revenue_account_id` int(11) NULL,
  `start_date` date,
  `end_date` date,
  `invoice_description` text,
  PRIMARY KEY  (`recurringinvoice_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `cv_main` (`cv_id`) ,
  FOREIGN KEY (`customer_account_id`) REFERENCES `transactions_accounts` (`account_id`),
  FOREIGN KEY (`revenue_account_id`) REFERENCES `transactions_accounts` (`account_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
  CREATE TABLE `invoices_recurringitems` (
  `recurringinvoiceitem_id` int(11) NOT NULL auto_increment,
  `recurringinvoice_id` int(11) NOT NULL,
  `invoiceitem_price_per` decimal(11,2) default '0.00',
  `invoiceitem_price_total` decimal(11,2) default '0.00',
  `invoiceitem_count` int(11) NOT NULL default 1,
  `inventory_id` int(11) NOT NULL,
  PRIMARY KEY  (`recurringinvoiceitem_id`),
  KEY `recurringinvoice_id` (`recurringinvoice_id`),
  FOREIGN KEY (`inventory_id`) REFERENCES `inventory_items` (`inventory_id`),
  FOREIGN KEY (`recurringinvoice_id`) REFERENCES `invoices_recurring` (`recurringinvoice_id`) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  CREATE TABLE `invoices_items` (
  `invoiceitem_id` int(11) NOT NULL auto_increment,
  `invoice_id` int(11) NOT NULL,
  `invoiceitem_price_per` decimal(11,2) default '0.00',
  `invoiceitem_price_total` decimal(11,2) default '0.00',
  `invoiceitem_count` int(11) NOT NULL default 1,
  `inventory_id` int(11) NOT NULL,
  PRIMARY KEY  (`invoiceitem_id`),
  FOREIGN KEY (`inventory_id`) REFERENCES `inventory_items` (`inventory_id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices_main` (`invoice_id`) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
  CREATE TABLE `invoices_accounts` (
  `account_id` int(11) NOT NULL UNIQUE,
  `account_type` varchar(20) NOT NULL default '',
  `default_account` smallint(1) default 0,
  FOREIGN KEY (`account_id`) REFERENCES `transactions_accounts` (`account_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
  CREATE TABLE `purchasing_accounts` (
  `account_id` int(11) NOT NULL UNIQUE,
  `account_type` varchar(20) NOT NULL default '',
  `default_account` smallint(1) default 0,
  FOREIGN KEY (`account_id`) REFERENCES `transactions_accounts` (`account_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  CREATE TABLE `cash_receipts` (
  `remit_no` int(11) NOT NULL auto_increment,
  `total_received` decimal(11,2) default '0.00',
  `remit_date` date default NULL,
  `customer_id` int(11) NULL,
  `total_applied` decimal(11,2) default '0.00',
  `gl_entry` int(11) default NULL UNIQUE,
  `comment` varchar(250) default NULL,
    `checkno` varchar(32) NOT NULL default '',
    `remittance_account` int(11) default NULL,
    `credit_or_clearing` int(1) NOT NULL default 0,
    `employee` int(11)NOT NULL,
    PRIMARY KEY  (`remit_no`),
    FOREIGN KEY (`customer_id`) REFERENCES `cv_main` (`cv_id`),
    FOREIGN KEY (`gl_entry`) REFERENCES `transactions_main` (`transaction_id`)  ON DELETE SET NULL,
    FOREIGN KEY (`remittance_account`) REFERENCES `transactions_accounts` (`account_id`),
    FOREIGN KEY (`employee`) REFERENCES `user_main` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
    CREATE TABLE `remittance_advice` (
    `ra_id` int(11) NOT NULL auto_increment,
    `remit_no` int(11) NOT NULL,
    `invoice_id` int(11) NOT NULL,
    `amount_applied` decimal(11,2) default '0.00',
    PRIMARY KEY  (`ra_id`),
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices_main` (`invoice_id`) ON DELETE CASCADE,
    FOREIGN KEY (`remit_no`) REFERENCES `cash_receipts` (`remit_no`)  ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
    CREATE TABLE `purchase_requisition` (
    `pr_id` int(11) NOT NULL auto_increment,
    `pr_date` date NOT NULL,
    `pr_requestor` int(11) NULL default NULL,
    `pr_approver` int(11) NULL default NULL,
    `vendor_id` int(11) NULL,
    `expense_account_id` int(11) NULL,
    `vendor_account_id` int(11) NULL,
    `clearing_account_id` int(11) NULL,
    `pr_pretaxtotal` decimal(11,2) default '0.00',
    `pr_tax` decimal(11,2) default '0.00',
    `pr_total` decimal(11,2) default '0.00',
    `auto_generated` smallint(1) default 0,
    `invoice_id`  int(11) NULL default NULL,
    `approved` smallint(1) default 0,
    `purchase_description` text,
    `po_id` int(11) default NULL,
    `pr_subtotal` decimal(11,2) default '0.00',
    `pr_discount` decimal(4,3) default '0.00',
    `pr_discount_total` decimal(11,2) default '0.00',
    PRIMARY KEY  (`pr_id`),
    FOREIGN KEY (`vendor_id`) REFERENCES `cv_main` (`cv_id`),
    FOREIGN KEY (`pr_approver`) REFERENCES `user_main` (`user_id`),
    FOREIGN KEY (`pr_requestor`) REFERENCES `user_main` (`user_id`),
    FOREIGN KEY (`vendor_account_id`) REFERENCES `transactions_accounts` (`account_id`) ,
    FOREIGN KEY (`expense_account_id`) REFERENCES `transactions_accounts` (`account_id`) ,
    FOREIGN KEY (`clearing_account_id`) REFERENCES `transactions_accounts` (`account_id`),
    FOREIGN KEY (`po_id`) REFERENCES `purchase_order`(`po_id`) ON DELETE SET NULL,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices_main`(`invoice_id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
    CREATE TABLE `invoices_requisitions` (
    `invoicerequisition_id` int(11) NOT NULL auto_increment,
    `invoice_id` int(11) NOT NULL,
    `pr_id` int(11) NOT NULL,
    PRIMARY KEY  (`invoicerequisition_id`),
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices_main` (`invoice_id`) ON DELETE CASCADE,
    FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisition` (`pr_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
    CREATE TABLE `purchase_order` (
    `po_id` int(11) NOT NULL auto_increment,
    `pr_id` int(11),
    `po_date` date NOT NULL,
    `vendor_id` int(11) NULL,
    `ship_via`varchar(25) NOT NULL default '',
    `buyer` int(11) NULL default NULL,
    `po_approver` int(11) NULL default NULL,
    `status` varchar(25) NOT NULL default '',
    `po_pretaxtotal` decimal(11,2) default '0.00',
    `po_tax` decimal(11,2) default '0.00',
    `po_total` decimal(11,2) default '0.00',
    `vendor_account_id` int(11) NULL,
  `clearing_account_id` int(11) NULL,
    `expense_account_id` int(11) NULL,
    `paid_in_full` int(1) NOT NULL default 0,
    `total_disbursed` decimal(11,2) default '0.00',
    `purchase_gl_entry` int(11) default NULL UNIQUE,
    `vendor_invoice_no` varchar(50) NOT NULL default '',
    `po_subtotal` decimal(11,2) default '0.00',
    `po_discount` decimal(4,3) default '0.00',
    `po_discount_total` decimal(11,2) default '0.00',
    PRIMARY KEY  (`po_id`),
    FOREIGN KEY (`vendor_id`) REFERENCES `cv_main` (`cv_id`) ,
    FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisition` (`pr_id`) ON DELETE CASCADE,
    FOREIGN KEY (`po_approver`) REFERENCES `user_main` (`user_id`),
    FOREIGN KEY (`buyer`) REFERENCES `user_main` (`user_id`),
    FOREIGN KEY (`vendor_account_id`) REFERENCES `transactions_accounts` (`account_id`) ,
    FOREIGN KEY (`expense_account_id`) REFERENCES `transactions_accounts` (`account_id`) ,
    FOREIGN KEY (`clearing_account_id`) REFERENCES `transactions_accounts` (`account_id`) ,
    FOREIGN KEY (`purchase_gl_entry`) REFERENCES `transactions_main` (`transaction_id`)  ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
  
    CREATE TABLE `purchase_items` (
    `purchaseitem_id` int(11) NOT NULL auto_increment,
    `pr_id` int(11) NOT NULL,
    `po_id` int(11) NULL,
    `purchaseitem_price_per` decimal(11,2) default '0.00',
    `purchaseitem_price_total` decimal(11,2) default '0.00',
    `purchaseitem_count` int(11) NOT NULL default 1,
    `inventory_id` int(11) NOT NULL,
    PRIMARY KEY  (`purchaseitem_id`),
    FOREIGN KEY (`po_id`) REFERENCES `purchase_order` (`po_id`) ON DELETE SET NULL,
    FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisition` (`pr_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
    CREATE TABLE `cash_disbursements` (
    `cd_no` int(11) NOT NULL auto_increment,
    `total_amount` decimal(11,2) default '0.00',
    `cd_date` date default NULL,
    `vendor_id` int(11) NULL,
    `total_applied` decimal(11,2) NOT NULL default '0.00',
    `gl_entry` int(11) default NULL UNIQUE,
    `comment` varchar(250) default NULL,
    `checkno` varchar(32) NOT NULL default '',
    `disbursement_account` int(11) default NULL,
    `employee` int(11) default NULL,
    `credit_or_clearing` tinyint(1) NOT NULL default 0,
    `is_refund` tinyint(1) NOT NULL default 0,
    PRIMARY KEY  (`cd_no`),
    FOREIGN KEY (`vendor_id`) REFERENCES `cv_main` (`cv_id`),
    FOREIGN KEY (`gl_entry`) REFERENCES `transactions_main` (`transaction_id`)  ON DELETE SET NULL,
    FOREIGN KEY (`employee`) REFERENCES `user_main` (`user_id`),
    FOREIGN KEY (`disbursement_account`) REFERENCES `transactions_accounts` (`account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
    CREATE TABLE `payment_relations` (
    `paymentrelation_id` int(11) NOT NULL auto_increment,
    `cd_no` int(11) NOT NULL,
    `po_id` int(11) NOT NULL,
    `amount_applied` decimal(11,2) default '0.00',
    PRIMARY KEY  (`paymentrelation_id`),
    FOREIGN KEY (`po_id`) REFERENCES `purchase_order` (`po_id`) ON DELETE CASCADE,
    FOREIGN KEY (`cd_no`)REFERENCES `cash_disbursements` (`cd_no`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
  
  
    CREATE TABLE `refund_advice` (
    `refund_id` int(11) NOT NULL auto_increment,
    `remit_no` int(11) NOT NULL,
    `cd_no` int(11) NOT NULL,
    `amount_applied` decimal(11,2) default '0.00',
    PRIMARY KEY  (`refund_id`),
    		FOREIGN KEY (`remit_no`) REFERENCES `cash_receipts` (`remit_no`) ON DELETE CASCADE,
    		FOREIGN KEY (`cd_no`) REFERENCES `cash_disbursements` (`cd_no`)  ON DELETE CASCADE
    		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
    		CREATE TABLE `invoices_mtm_files` (
    		`invoice_file_id` int(11) NOT NULL auto_increment,
    		`file_id` int(11) NOT NULL,
    		`invoice_id` int(11) NOT NULL,
    		PRIMARY KEY  (`invoice_file_id`),
    		FOREIGN KEY (`file_id`) REFERENCES `files_main` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    		FOREIGN KEY (`invoice_id`) REFERENCES `invoices_main` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
    		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
  
  
  
?>
