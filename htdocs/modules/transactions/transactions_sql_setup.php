<?php


$install_tables_string ="
DROP TABLE IF EXISTS `transactions_accounttype`;
CREATE TABLE `transactions_accounttype` (
`accounttype_id` int(11) NOT NULL auto_increment,
`accounttype_name` varchar(20) NOT NULL default '',
`accounttype_sign` enum('CREDIT','DEBIT') NOT NULL default 'CREDIT',
PRIMARY KEY  (`accounttype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO transactions_accounttype ( accounttype_id, accounttype_name, accounttype_sign )
VALUES (1, 'ASSET', 'DEBIT' );
INSERT INTO transactions_accounttype ( accounttype_id, accounttype_name, accounttype_sign )
VALUES (2, 'LIABILITY', 'CREDIT' );
INSERT INTO transactions_accounttype ( accounttype_id, accounttype_name, accounttype_sign )
VALUES (3, 'EQUITY', 'CREDIT' );
INSERT INTO transactions_accounttype ( accounttype_id, accounttype_name, accounttype_sign )
VALUES (4, 'INCOME', 'CREDIT' );
INSERT INTO transactions_accounttype ( accounttype_id, accounttype_name, accounttype_sign )
VALUES (5, 'EXPENSE', 'DEBIT' );
INSERT INTO transactions_accounttype ( accounttype_id, accounttype_name, accounttype_sign )
VALUES (6, 'GAIN', 'CREDIT' );
INSERT INTO transactions_accounttype ( accounttype_id, accounttype_name, accounttype_sign )
VALUES (7, 'LOSS', 'DEBIT' );


DROP TABLE IF EXISTS `transactions_accounts`;
CREATE TABLE `transactions_accounts` (
`account_id` int(11) NOT NULL auto_increment,
`account_name` varchar(50) default '',
`accounttype_id` int(11) NOT NULL default '0',
`account_memo` varchar(70) default '',
`account_starting` decimal(7,2) default '0.00',
`account_current` enum('Y','N') NOT NULL default 'Y',
`account_left` int(11) default NULL,
`account_right` int(11) default NULL,
`account_balance` decimal(9,2) default NULL,
`account_subtotal` decimal(9,2) default NULL,
`account_fullname` varchar(200) default '',
`account_parent` int(11) NOT NULL default '0',
`account_reconcile_date` date default NULL,
`account_flagged` int default 0,
`account_locked` int default 0,
`account_open_date` date NOT NULL default '0000-00-00',
`account_close_date` date NOT NULL default '0000-00-00',
`account_code` varchar(50) default '',
PRIMARY KEY  (`account_id`),
KEY `accounttype_id` (`accounttype_id`),
CONSTRAINT `transactions_accounts_ibfk_1` FOREIGN KEY (`accounttype_id`) REFERENCES `transactions_accounttype` (`accounttype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `transactions_accounts_mtm_users`;
CREATE TABLE `transactions_accounts_mtm_users` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL default '0',
`account_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `user_id` (`user_id`),
KEY `account_id` (`account_id`),
CONSTRAINT `transactions_accounts_mtm_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_main` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `transactions_accounts_mtm_users_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `transactions_accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



DROP TABLE IF EXISTS `transactions_main`;
CREATE TABLE `transactions_main` (
`transaction_id` int(11) NOT NULL auto_increment,
`transaction_date` date default NULL,
`transaction_comment` varchar(250) default NULL,
`transaction_amount` decimal(11,2) default '0.00',
`transaction_checkno` varchar(32) NOT NULL default '',
`transaction_reconcile` char(1) default 'n',
`transaction_reconcile_date` date default NULL,
`is_split` smallint(1) default 0,
PRIMARY KEY  (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    
DROP TABLE IF EXISTS `transactions_debit_credit`;
CREATE TABLE `transactions_debit_credit` (
  `transaction_dc_id` int(11) NOT NULL auto_increment,
  `transaction_account` int(11) default '0',
  `transaction_id` int(11) default '0',
  `transaction_dc_amount` decimal(11,2) default '0.00',
  `transaction_dc` enum('DEBIT','CREDIT') NOT NULL default 'DEBIT',
  PRIMARY KEY  (`transaction_dc_id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `transaction_account` (`transaction_account`),
  FOREIGN KEY (`transaction_id`) REFERENCES `transactions_main` (`transaction_id`)  ON DELETE CASCADE,
  FOREIGN KEY (`transaction_account`) REFERENCES `transactions_accounts` (`account_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    
DROP TABLE IF EXISTS `transactions_files`;
CREATE TABLE `transactions_files` (
`transaction_to_file_id` int(11) NOT NULL auto_increment,
`file_id` int(11) default '0',
`transaction_id` int(11) default '0',
PRIMARY KEY  (`transaction_to_file_id`),
KEY `file_id` (`file_id`),
KEY `transaction_id` (`transaction_id`),
CONSTRAINT `transactions_files_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files_main` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `transactions_files_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions_main` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
if($ACTION == "Install Module")
{
    echo "CREATING DATABASE TABLES FOR MODULE";
    $mysqli = new mysqli($DB_SETTINGS['dbhost'], $DB_SETTINGS['user'], $DB_SETTINGS['pass'], $DB_SETTINGS['dbname']);
    $stmt = $mysqli->multi_query($install_tables_string);
    //echo $install_tables_string;
}

if($ACTION == "Load Defaults")
{
    require_once("../../../../openMobas/classes/TransactionAccount.class.php");
    $transactionAccount = new TransactionAccount($dbh, $FRAMEWORK);
    
    echo "included transactions<BR />";
    $transactionAccount->add_accountnew("NULL", "ASSETS", 0, 1, "", "Y");
    $transactionAccount->add_accountnew("NULL", "ACCTS RECEIVABLE", 1, 1, "", "Y");
    $transactionAccount->add_accountnew("NULL", "CASH", 1, 1, "", "Y");
    $transactionAccount->add_accountnew("NULL", "LIABILITIES", 0, 2, "", "Y");
    $transactionAccount->add_accountnew("NULL", "EQUITY", 0, 3, "",  "Y");
    $transactionAccount->add_accountnew("NULL", "INCOME", 0, 4, "",  "Y");
    $transactionAccount->add_accountnew("NULL", "EXPENSES", 0, 5, "",  "Y");
    echo "Added Base Account Tree<BR />";
}

?>
