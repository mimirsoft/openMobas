<?php

$actions =  array();
$actions[] = "access_module";


$objects = array();
$objects[] = "tenants_module";


CREATE TABLE `tenants_main` (
`tenant_id` int(11) NOT NULL auto_increment,
`tenant_current` enum('Y','N') NOT NULL default 'Y',
`tenant_rent` decimal(8,2) NOT NULL default '0.00',
`tenant_deposit` decimal(8,2) NOT NULL default '0.00',
`inventory_id` int(11) default NULL,
`lease_start_date` date default NULL,
`lease_end_date` date default NULL,
`thirty_day_date` date default NULL,
`inspection_date` date default NULL,
`move_out_date` date default NULL,
`refund_date` date default NULL,
`recurringinvoice_id` int(11) default NULL,
`cv_id` int(11) NOT NULL,
PRIMARY KEY  (`tenant_id`),
FOREIGN KEY (`recurringinvoice_id`) REFERENCES `invoices_recurring` (`recurringinvoice_id`)ON DELETE SET NULL,
FOREIGN KEY (`inventory_id`) REFERENCES `inventory_items` (`inventory_id`)ON DELETE SET NULL,
FOREIGN KEY (`cv_id`) REFERENCES `cv_main` (`cv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



?>
