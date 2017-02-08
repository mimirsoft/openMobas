<?php
/*
 * 
    This file is part of WebPropMan
    Copyright (C) 2011, Kevin Milhoan

    WebPropMan is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    WebPropMan is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

   Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

*
*/
require_once("CV_Main.class.php");
require_once("Purchase_Requisition.class.php");
require_once("Purchasing_System.class.php");
require_once("Purchase_Order.class.php");

class InvoiceException extends Exception  {
	public $message;
	public function __construct($message)
	{
		$this->message = $message;
	}
}

class Invoice{

	public static function create_invoice($invoice_id, $cr, $pretax, $tax, $total, $bname, $badd1, $badd2, $bcity, $bstate, $bzip, $sname, $sadd1, $sadd2, $scity, $sstate, $szip, $cv_id, $customer_acct, $revenue_acct)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("INSERT INTO invoices_main
                                        SET invoice_id=:1:, 
                                            invoice_clientreference=:2:, 
                                            invoice_pretaxtotal=:3:, 
                                            invoice_tax=:4:, 
                                            invoice_total=:5:,
                                            billto_name=:6:,
                                            billto_address1=:7:,
                                            billto_address2=:8:,
                                            billto_city=:9:,
                                            billto_state=:10:,
                                            billto_zip=:11:,
                                            shipto_name=:12:,
                                            shipto_address1=:13:,
                                            shipto_address2=:14:,
                                            shipto_city=:15:,
                                            shipto_state=:16:,
                                            shipto_zip=:17:,
                                            customer_id=:18:,
                                            customer_account_id=:19:,
                                            revenue_account_id=:20:");
		$stmt->execute($invoice_id, $cr, $pretax, $tax, $total, $bname, $badd1, $badd2, $bcity, $bstate, $bzip, $sname, $sadd1, $sadd2, $scity, $sstate, $szip, $cv_id, $customer_acct, $revenue_acct);
		return mysql_insert_id();

	}
	public static function update_invoice($invoice_id, $cr, $bname, $badd1, $badd2, $bcity, $bstate, $bzip,
	$sname, $sadd1, $sadd2, $scity, $sstate, $szip, $revenue_acct)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_main
                                    SET     invoice_clientreference=:2:, 
                                            billto_name=:3:,
                                            billto_address1=:4:,
                                            billto_address2=:5:,
                                            billto_city=:6:,
                                            billto_state=:7:,
                                            billto_zip=:8:,
                                            shipto_name=:9:,
                                            shipto_address1=:10:,
                                            shipto_address2=:11:,
                                            shipto_city=:12:,
                                            shipto_state=:13:,
                                            shipto_zip=:14:,
                                            revenue_account_id=:15:
                                        WHERE invoice_id=:1:");
		$stmt->execute($invoice_id, $cr, $bname, $badd1, $badd2, $bcity, $bstate, $bzip, $sname, $sadd1, $sadd2, $scity, $sstate, $szip, $revenue_acct);
		return mysql_insert_id();

	}
	public static function delete_invoice($invoice_id)
	{
		$invoice = Invoice::get_invoice($invoice_id);
		if($invoice['invoice_gl_entry'] != NULL)
		{
			throw new InvoiceException("Cannot delete a charged Invoice, Remove Charge First");
		}
		//we must unapply the remittances, or else they will remain applied against an invoice that not longer exists.
		Invoice::unapplyRemittancesAgainstInvoice($invoice_id);
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("DELETE FROM invoices_main
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id);
		return mysql_insert_id();
	}
	public static function update_invoice_total($invoice_id)
	{
		$dbh = new DB_Mysql();
		//if there are no invoice items, this will return a null, we need a zero
		$stmt = $dbh->prepare("SELECT COALESCE(SUM(invoiceitem_price_total),0) AS total
                                 FROM invoices_items
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id);
		$row = $stmt->fetch_assoc();
		$total = $row['total'];
		$stmt = $dbh->prepare("UPDATE invoices_main
                                  SET invoice_total=$total
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id);
		return mysql_insert_id();

	}
	public static function update_charged($invoice_id, $dc)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_main
                                  SET invoice_gl_entry=$dc
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id);
		return mysql_insert_id();

	}
	public static function update_description($invoice_id, $dc)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_main
                                  SET invoice_description=:2:
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id, $dc);
		return mysql_insert_id();

	}
	public static function update_customer_account_id($invoice_id, $dc)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_main
                                  SET customer_account_id=:2:
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id, $dc);
		return mysql_insert_id();

	}
	public static function update_date_charged($invoice_id, $date)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_main
                                  SET date_charged=:2:
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id, $date);
		return mysql_insert_id();

	}
	public static function update_paid_in_full($invoice_id, $date)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_main
                                  SET paid_in_full=:2:
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id, $date);
		return mysql_insert_id();

	}
	public static function update_auto_purchases_complete($invoice_id, $date)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_main
                                  SET auto_purchases_complete=:2:
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id, $date);
		return mysql_insert_id();

	}


	public static function add_invoiceitem($invoice_id, $cost_per, $count, $inventory_id)
	{
		$total = $cost_per*$count;
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("INSERT INTO invoices_items
                                       SET invoiceitem_id=NULL, 
                                           invoice_id=:1:, 
                                           invoiceitem_price_per=:2:, 
                                           invoiceitem_price_total=$total, 
                                           invoiceitem_count=:3:,
                                           inventory_id=:4:");
		$stmt->execute($invoice_id, $cost_per, $count, $inventory_id);
		Invoice::update_invoice_total($invoice_id);
		return mysql_insert_id();
	}
	public static function update_invoiceitem_count($invoiceitem_id, $count)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_items
                                  SET invoiceitem_count=:2:
                                WHERE invoiceitem_id=:1:");
		$stmt->execute($invoiceitem_id, $count);
		Invoice::update_invoiceitem_total($invoiceitem_id);
		$invoice_item = Invoice::get_invoiceitem($invoiceitem_id);
		Invoice::update_invoice_total($invoice_item['invoice_id']);
		return mysql_insert_id();

	}
	public static function uncharge_invoice($invoice_id)
	{
		$invoice_info = Invoice::get_invoice($invoice_id);
		Invoice::unapplyRemittancesAgainstInvoice($invoice_id);
		try{
			transaction::delete_transaction($invoice_info['invoice_gl_entry']);
		}
		catch(TransactionException $exception)
		{
			throw new InvoiceException($exception->message);
		}
	}
	public static function charge_invoice($invoice_id, $date)
	{
		$invoice_info = Invoice::get_invoice($invoice_id);
		$dc_line['transaction_dc'] = 'CREDIT';
		$dc_line['transaction_dc_amount'] = $invoice_info['invoice_total'];
		$dc_line['transaction_account'] = $invoice_info['revenue_account_id'];
		$dc_set[] = $dc_line;
		$dc_line['transaction_dc'] = 'DEBIT';
		$dc_line['transaction_dc_amount'] = $invoice_info['invoice_total'];
		$dc_line['transaction_account'] = $invoice_info['customer_account_id'];
		$dc_set[] = $dc_line;
		$comment = 'INV #'.$invoice_info['invoice_id']." charged to CV#".$invoice_info['cv_id']." - ".$invoice_info['cv_name'] ;
		$checkno = '';
		try{
			$transaction_id = transaction::add_transaction('NULL', $date, $comment, $checkno, $dc_set, false);
		}
		catch(TransactionException $exception)
		{
			throw new InvoiceException($exception->message);
		}
		Invoice::update_charged($invoice_id, $transaction_id);
		Invoice::update_date_charged($invoice_id, $date);
		//update CV total balance owed
		Invoice::update_cv_receivables_total($invoice_info['cv_id']);		
	}

	public static function update_invoiceitem_price_per($invoiceitem_id, $per)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_items
                                  SET invoiceitem_price_per=:2:
                                WHERE invoiceitem_id=:1:");
		$stmt->execute($invoiceitem_id, $per);
		Invoice::update_invoiceitem_total($invoiceitem_id);
		$invoice_item = Invoice::get_invoiceitem($invoiceitem_id);
		Invoice::update_invoice_total($invoice_item['invoice_id']);
		return mysql_insert_id();
	}

	public static function update_invoiceitem_total($invoiceitem_id)
	{
		$item = Invoice::get_invoiceitem($invoiceitem_id);
		$total = $item['invoiceitem_price_per']*$item['invoiceitem_count'];
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_items
                                  SET invoiceitem_price_total=:2:
                                WHERE invoiceitem_id=:1:");
		$stmt->execute($invoiceitem_id, $total);
		return mysql_insert_id();

	}
    public static function update_cv_receivables_total($cv_id)
    {
        //get all invoices
        $total_owed = Invoice::sum_open_invoices_of_customer($cv_id);
        //update the totals for the cv_main
        CV_Main::update_receivable_total($cv_id,$total_owed);
    }
    public static function unapplyRemittancesAgainstInvoice($invoice_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                 FROM remittance_advice
                                WHERE invoice_id=:1:");
        $stmt->execute($invoice_id);
        $row = $stmt->fetchall_assoc();
        //get all invoices
        //now delete all current advice
        $stmt = $dbh->prepare("DELETE 
                                 FROM remittance_advice
                                WHERE invoice_id=:1:");
        $stmt->execute($invoice_id);
        foreach($row as $r)
        {
        	Cash_Receipts::update_total_applied($r['remit_no']);
        }
        //now update the total remitted, and the paid in full flagg
        Invoice::update_total_remitted($invoice_id);
        return;
    }
	public static function update_total_remitted($invoice_id)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT SUM(amount_applied) AS total_applied
                                 FROM remittance_advice
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id);
		$row = $stmt->fetch_assoc();
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("UPDATE invoices_main
                                  SET total_remitted='$row[total_applied]'
                                WHERE invoice_id=:1:");
		$stmt->execute($invoice_id);
		$invoice_info = Invoice::get_invoice($invoice_id);
		if($invoice_info['total_remitted'] == $invoice_info['invoice_total'])
		{
			Invoice::update_paid_in_full($invoice_id, 1);
			if($invoice_info['auto_purchases_complete'] == 0)
			{
				//if it is paid in full autogenerate any needed PRs
				Invoice::create_auto_purchases($invoice_id);
				Invoice::update_auto_purchases_complete($invoice_id, 1);
			}
		}
		else
		{
			Invoice::update_paid_in_full($invoice_id, 0);

		}
        Invoice::update_cv_receivables_total($invoice_info['cv_id']);       
		
	}
	public static function create_auto_purchases($invoice_id)
	{
		//now check all inventory items on this invoice for auto PO
		$invoice_items = Invoice::getall_invoiceitems($invoice_id);
		$invoice_info = Invoice::get_invoice($invoice_id);
        foreach($invoice_items as $invoice_item)
		{
			if($invoice_item['on_sale_auto_purchase'])
			{
				//group them by vendor
				$vendor_items[$invoice_item['cv_id']][] = $invoice_item;
			}

		}
		//generate a PR for each vendor
		//get default clearing account
		$clearing_info = Purchasing_System::get_default_clearing_account();
		$expense_info = Purchasing_System::get_default_expense_account();
		//get default expense/inventory account
		if(is_array($vendor_items))
		{
		foreach($vendor_items as $vendor_key => $item_array)
		{
			$vendor_info = CV_Main::get_cv_from_id($vendor_key);
			$pr_id = Purchase_Requisition::create_purchase_request("NULL", date("Ymd"), $vendor_key, $expense_info['account_id'], $vendor_info['GL_AP_id'], $clearing_info['account_id']);
			Purchase_Requisition::update_auto_generated($pr_id, 1);
			Purchase_Requisition::update_description($pr_id, $invoice_info['invoice_description']);
			Purchase_Requisition::updateInvoiceId($pr_id, $invoice_info['invoice_id']);
			foreach($item_array as $invoice_item)
			{
				Purchase_Requisition::add_pr_item($pr_id, $invoice_item['wholesale_price'],  $invoice_item['invoiceitem_count'], $invoice_item['inventory_id']);
			}
			//generate a PO for each PR, these are automatic and do not need approval
			Purchase_Requisition::update_approved($pr_id, 1);
            Purchase_Requisition::generate_purchaseorder($pr_id);
            
		}
		}

	}
	 

	public static function delete_invoiceitem($invoiceitem_id)
	{
		$invoice_item = Invoice::get_invoiceitem($invoiceitem_id);
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("DELETE FROM invoices_items
                                WHERE invoiceitem_id=:1:");
		$stmt->execute($invoiceitem_id);
		Invoice::update_invoice_total($invoice_item['invoice_id']);
		return mysql_insert_id();
	}

	public static function getall_invoiceitems($ID)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT *
                                 FROM invoices_items 
                           LEFT JOIN inventory_items 
                                   ON inventory_items.inventory_id = invoices_items.inventory_id 
                           LEFT JOIN  cv_main 
                                   ON inventory_items.cv_id = cv_main.cv_id 
                           LEFT JOIN inventory_types
                                   ON inventory_items.item_type = inventory_types.inventorytype_id 
                                WHERE invoice_id = :1:");
		$stmt->execute($ID);
		return $stmt->fetchall_assoc();
	}
	public static function get_invoiceitem($ID)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT *
                                 FROM invoices_items 
                           LEFT JOIN inventory_items 
                                   ON inventory_items.inventory_id = invoices_items.inventory_id 
                           LEFT JOIN inventory_types
                                   ON inventory_items.item_type = inventory_types.inventorytype_id 
                                WHERE invoices_items.invoiceitem_id = :1:");
		$stmt->execute($ID);
		return $stmt->fetch_assoc();
	}

	public static function get_invoice($ID)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT *, ra.account_fullname AS revenueaccount_fullname,  ca.account_fullname as customeraccount_fullname
                                 FROM invoices_main 
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = invoices_main.customer_id 
                           INNER JOIN transactions_accounts AS ca
                                   ON ca.account_id = invoices_main.customer_account_id 
                           INNER JOIN transactions_accounts AS ra
                                   ON ra.account_id = invoices_main.revenue_account_id 
                                WHERE invoice_id = :1:");
		$stmt->execute($ID);
		return $stmt->fetch_assoc();
	}
	public static function verify_remittance_advice()
	{
		$remittances = Invoice::getall_invoices();
		foreach($remittances as $row)
		{
			Invoice::update_total_remitted($row['invoice_id']);
		}
	}
	public static function getall_invoices()
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM invoices_main INNER JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id");
		$stmt->execute();
		return $stmt->fetchall_assoc();
	}
	public static function getAllInvoicesWithItem($item)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM invoices_main INNER JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id
		 							INNER JOIN invoices_items 
                                   ON invoices_items.invoice_id = invoices_main.invoice_id
                                WHERE invoices_items.inventory_id = :1:");
		$stmt->execute($item);
		return $stmt->fetchall_assoc();
	}
	public static function getAllInvoicesWithItemBetweenDates($item, $startdate, $stopdate)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM invoices_main INNER JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id
		 							INNER JOIN invoices_items 
                                   ON invoices_items.invoice_id = invoices_main.invoice_id
                                WHERE invoices_items.inventory_id = :1:
                                	AND date_charged >= '$startdate'
                                    AND date_charged <= '$stopdate' 
                                    ORDER BY date_charged");
		$stmt->execute($item);
		return $stmt->fetchall_assoc();
	}
	public static function get_requisitions_from_invoice($ID)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT *
                                 FROM invoices_requisitions 
                                WHERE invoice_id = :1:");
		$stmt->execute($ID);
		return $stmt->fetchall_assoc();
	}
    public static function getall_invoices_of_customer($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, invoices_main.invoice_id, invoice_total-total_remitted AS remaining_balance,
        							GROUP_CONCAT(invoices_mtm_files.file_id) AS file_string 
        						FROM invoices_main 
        						LEFT JOIN invoices_mtm_files
                        		ON  invoices_main.invoice_id = invoices_mtm_files.invoice_id 
                        		INNER JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id
                                    WHERE customer_id = :1:
                                    GROUP BY invoices_main.invoice_id ");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_open_invoices_of_customer($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *,invoice_total-total_remitted AS remaining_balance FROM invoices_main INNER JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id
                                    WHERE paid_in_full=0 AND customer_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function sum_open_invoices_of_customer($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(invoice_total-total_remitted)AS total_owed FROM invoices_main 
                                INNER JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id
                                    WHERE paid_in_full=0 AND customer_id = :1:");
        $stmt->execute($ID);
        $row = $stmt->fetch_assoc();
        return $row['total_owed'];
    }
    public static function getall_customers_purchasing_item($ID)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM cv_main 
						   INNER JOIN invoices_main 
						           ON cv_main.cv_id = invoices_main.customer_id
                           INNER JOIN invoices_items 
                                   ON invoices_items.invoice_id = invoices_main.invoice_id
                                WHERE invoices_items.inventory_id = :1:
                             GROUP BY cv_main.cv_id");
		$stmt->execute($ID);
		return $stmt->fetchall_assoc();
	}
	public static function getall_invoices_of_customer_last30days($ID)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM invoices_main INNER JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id
                                    WHERE customer_id = :1:
                                    AND CURDATE()-date_charged < 31");
		$stmt->execute($ID);
		return $stmt->fetchall_assoc();
	}
    public static function getall_invoices_of_customer_between_dates($ID, $startdate, $stopdate)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM invoices_main INNER JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id
                                    WHERE customer_id = :1:
                                    AND date_charged >= '$startdate'
                                    AND date_charged <= '$stopdate' 
                                    ORDER BY date_charged");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }	public static function getall_openinvoices()
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM invoices_main
        				    INNER JOIN cv_main 
        				    ON cv_main.cv_id = invoices_main.customer_id  
        				    WHERE invoice_gl_entry IS NULL");
		$stmt->execute();
		return $stmt->fetchall_assoc();
	}
	public static function check_gl_integrity()
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM invoices_main
                           INNER JOIN cv_main 
        				           ON cv_main.cv_id = invoices_main.customer_id  
        				   INNER JOIN transactions_main AS tm 
        				           ON tm.transaction_id = invoices_main.invoice_gl_entry
        				   INNER JOIN transactions_debit_credit AS deb
        				           ON tm.transaction_id = deb.transaction_id
        				   INNER JOIN transactions_debit_credit AS cred
        				           ON tm.transaction_id = cred.transaction_id
        				        WHERE (deb.transaction_dc = 'DEBIT' 
        				          AND cred.transaction_dc = 'CREDIT') 
        				    	  AND (deb.transaction_account != invoices_main.customer_account_id 
        				    	   OR cred.transaction_account !=  invoices_main.revenue_account_id )");
		$stmt->execute();
		return $stmt->fetchall_assoc();
	}

    public static function getall_unpaidinvoices()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM invoices_main
                                    INNER JOIN cv_main 
                                    ON cv_main.cv_id = invoices_main.customer_id 
                                    WHERE invoice_gl_entry IS NOT NULL AND paid_in_full = 0
                                    ORDER by date_charged");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
	public static function getall_brokeninvoices()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("Select * FROM invoices_items 
                        LEFT join invoices_main ON invoices_items.invoice_id = invoices_main.invoice_id 
                        LEFT JOIN cv_main ON cv_main.cv_id = invoices_main.customer_id 
                        where inventory_id not in (select inventory_id from inventory_items) ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }

    function linkFile($file, $invoice)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO invoices_mtm_files 
                                           SET file_id=:1:, 
                                               invoice_id=:2:");
        $stmt->execute($file, $invoice);
    }
    function getAllFilesOfInvoice($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM invoices_mtm_files as imf, files_main as fm
                                WHERE imf.invoice_id=:1: 
                                AND imf.file_id = fm.file_id");
        $stmt->execute($ID);
        $files = $stmt->fetchall_assoc();
        return $files;
    }
        
}

?>
