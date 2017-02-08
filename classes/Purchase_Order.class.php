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
require_once("Cash_Receipt.class.php");
require_once("Cash_Disbursement.class.php");

class PurchaseOrderException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}

class Purchase_Order{

    public static function create_purchase_order($po_id, $pr_id, $date, $vendor, $expense_account, $payable, $clearing)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO purchase_order
                                        SET po_id=:1:, 
                                            pr_id=:2:, 
                                            po_date=:3:, 
                                            vendor_id=:4:,
                                            expense_account_id=:5:,
                                            vendor_account_id=:6:,
                                            clearing_account_id=:7:");
        $stmt->execute($po_id, $pr_id, $date, $vendor, $expense_account, $payable, $clearing);
        $po_id = mysql_insert_id();
        Purchase_Order::update_po_total($po_id);
        return $po_id;

    }
    public static function delete_purchase_order($po_id)
    {
        $dbh = new DB_Mysql();
        //first, find any remittances applied to this PO
        $disbursements = Cash_Disbursements::getall_disbursements_against_po($po_id);
        // then delete all payment relations for this PO
        Cash_Disbursements::delete_payment_relations_for_po($po_id);
        // then recalculate those remittances
        foreach($disbursements as $row)
        {
            if($row['credit_or_clearing'] == 1)
            {
                //delete the matching clearing Remittanceif($remit_info['credit_or_clearing'] == 1)
                Cash_Disbursements::delete_disbursement($row['cd_no']);
                //delete the matching clearing Remittance
                Cash_Receipts::deleteMatchingClearingRemittance($row['total_amount'], $row['vendor_id']);
            }
            else
            {
                Cash_Disbursements::update_total_applied($row['cd_no']);
            }
        }
        // if they were a clearing, remove the matching clearing
        $stmt = $dbh->prepare("DELETE FROM purchase_order 
                                  WHERE po_id=:1:");
        $stmt->execute($po_id);
        return mysql_insert_id();
    }
    public static function update_po_total($po_id)
    {
        $po_info = Purchase_Order::get_purchase_order($po_id);
		Purchase_Order::update_subtotal($po_id);
		Purchase_Order::update_discount_total($po_id);
        $dbh = new DB_Mysql();
        //update the total
        $stmt = $dbh->prepare("UPDATE purchase_order
                                  SET po_total=po_subtotal-po_discount_total
                                WHERE po_id=:1:");
        $stmt->execute($po_id);
        Purchase_Order::update_total_disbursed($po_id);
        return mysql_insert_id();
    }
    public static function update_purchase_recorded($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_order 
                                  SET purchase_gl_entry=$dc
                                WHERE po_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();

    }
    //this no longer does anything...this is currently not implemented
    public static function update_invoice_recorded($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_order 
                                  SET invoice_recorded=$dc
                                WHERE po_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();
    }
    
    public static function update_date($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_order 
                                  SET po_date=:2:
                                WHERE po_id=:1:");
        $stmt->execute($invoice_id, $dc);
        return mysql_insert_id();
    }   
    public static function update_discount($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_order 
                                  SET po_discount=:2:
                                WHERE po_id=:1:");
        $stmt->execute($invoice_id, $dc);
        //now update the discount total
        Purchase_Order::update_discount_total($invoice_id);
        //now update the total
        Purchase_Order::update_po_total($invoice_id);
        //now check to see if it is paid in full
        Purchase_Order::update_total_disbursed($invoice_id);
        return mysql_insert_id();
    } 
    public static function update_discount_total($po_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_order 
                                  SET po_discount_total=po_discount*po_subtotal
                                WHERE po_id=:1:");
        $stmt->execute($po_id);
        //now update the total
        return mysql_insert_id();
    }
    public static function update_subtotal($po_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT COALESCE(SUM(`purchaseitem_price_total`),0.00) AS total  
                                 FROM purchase_items
                                WHERE po_id=:1:");
        $stmt->execute($po_id);
        $row = $stmt->fetch_assoc();
        //update the subtotal
        $stmt = $dbh->prepare("UPDATE purchase_order 
                                  SET po_subtotal=$row[total]
                                WHERE po_id=:1:");
        $stmt->execute($po_id);
    }   
    
    public static function update_paid_in_full($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_order 
                                  SET paid_in_full=$dc
                                WHERE po_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();
    }
    
    public static function getall_purchaseorders()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_order INNER JOIN cv_main ON cv_main.cv_id = purchase_order.vendor_id");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_purchaseorders_of_vendor($cv_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_order 
                              INNER JOIN cv_main ON cv_main.cv_id = purchase_order.vendor_id 
                              INNER JOIN purchase_requisition ON purchase_requisition.pr_id = purchase_order.pr_id 
                              LEFT JOIN invoices_main ON purchase_requisition.invoice_id = invoices_main.invoice_id 
                              WHERE purchase_order.vendor_id = :1:
                              ORDER by po_date");
        $stmt->execute($cv_id);
        return $stmt->fetchall_assoc();
    }
    
    public static function sum_purchaseorder_of_vendor_date($cv_id, $startdate, $stopdate)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(po_total) FROM purchase_order 
                              WHERE purchase_order.vendor_id = :1:
                              AND po_date >=:2:
                              AND po_date <=:3:");
        $stmt->execute($cv_id, $startdate, $stopdate);
        return $stmt->fetch_assoc();
    }    
    
    public static function getall_purchaseorders_of_vendor_last30days($cv_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_order 
                              INNER JOIN cv_main ON cv_main.cv_id = purchase_order.vendor_id
                              INNER JOIN purchase_requisition ON purchase_requisition.pr_id = purchase_order.pr_id 
                              WHERE purchase_order.vendor_id = :1:
                              AND CURDATE()-po_date < 31");
        $stmt->execute($cv_id);
        return $stmt->fetchall_assoc();
    }
    public static function getall_purchaseorders_of_vendor_between_dates($cv_id, $start, $stop)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_order 
                              INNER JOIN cv_main ON cv_main.cv_id = purchase_order.vendor_id
                              INNER JOIN purchase_requisition ON purchase_requisition.pr_id = purchase_order.pr_id 
                              WHERE purchase_order.vendor_id = :1:
                              AND po_date >= '$start'
                              AND po_date <= '$stop'
                              ORDER BY po_date");
        $stmt->execute($cv_id);
        return $stmt->fetchall_assoc();
    }
    public static function getall_unpaidpurchaseorders()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_order INNER JOIN cv_main ON cv_main.cv_id = purchase_order.vendor_id WHERE paid_in_full = 0");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_unpaidpurchaseorders_by_vendor()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_order INNER JOIN cv_main ON cv_main.cv_id = purchase_order.vendor_id WHERE paid_in_full = 0 ORDER BY cv_name");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_unpaidpurchaseorders_of_vendor($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_order 
                                INNER JOIN cv_main ON cv_main.cv_id = purchase_order.vendor_id 
                                LEFT JOIN  purchase_requisition 
	 							ON purchase_order.pr_id = purchase_requisition.pr_id
	 							WHERE paid_in_full = 0
                                AND purchase_order.vendor_id=:1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function get_purchase_order($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, ea.account_fullname AS expenseaccount_fullname,
        							va.account_fullname AS vendoraccount_fullname 
                                 
                                 FROM purchase_order 
        						 INNER JOIN cv_main
        						ON cv_main.cv_id = purchase_order.vendor_id
                                   INNER JOIN transactions_accounts AS ea
                                   ON ea.account_id = purchase_order.expense_account_id 
                                   INNER JOIN transactions_accounts AS va
                                   ON va.account_id = purchase_order.vendor_account_id 
                                    WHERE po_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
	public static function verify_remittance_advice()
	{
		$remittances = Purchase_Order::getall_purchaseorders();
		foreach($remittances as $row)
		{
			Purchase_Order::update_total_disbursed($row['po_id']);
		}
	}
    public static function update_total_disbursed($po_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(amount_applied) AS total_applied 
                                 FROM payment_relations
                                WHERE po_id=:1:");
        $stmt->execute($po_id);
        $row = $stmt->fetch_assoc();
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_order
                                  SET total_disbursed='$row[total_applied]'
                                WHERE po_id=:1:");
        $stmt->execute($po_id);
        $po_info = Purchase_Order::get_purchase_order($po_id);
        if($po_info['total_disbursed'] == $po_info['po_total'])
        {
            Purchase_Order::update_paid_in_full($po_id, 1);

        }
        else
        {
            Purchase_Order::update_paid_in_full($po_id, 0);

        }
    }    
    public static function get_purchase_order_from_pr($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM purchase_order 
        						 INNER JOIN cv_main
        						ON cv_main.cv_id = purchase_order.vendor_id
                                WHERE pr_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function getAllPurchasesWithItemBetweenDates($item, $startdate, $stopdate)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM purchase_order INNER JOIN cv_main ON cv_main.cv_id = purchase_order.vendor_id
		 						LEFT JOIN  purchase_requisition 
		 							ON purchase_order.pr_id = purchase_requisition.pr_id
                                   		INNER JOIN purchase_items 
                                   ON purchase_items.po_id = purchase_order.po_id
                                WHERE purchase_items.inventory_id = :1:
                                	AND po_date >= '$startdate'
                                    AND po_date <= '$stopdate' 
                                    ORDER BY po_date");
		$stmt->execute($item);
		return $stmt->fetchall_assoc();
	}      
    public static function getAllPurchasesReqWithItemBetweenDates($item, $startdate, $stopdate)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM purchase_requisition INNER JOIN cv_main ON cv_main.cv_id = purchase_requisition.vendor_id
		 							INNER JOIN purchase_items 
                                   ON purchase_items.pr_id = purchase_requisition.pr_id
                                WHERE purchase_items.inventory_id = :1:
                                	AND pr_date >= '$startdate'
                                    AND pr_date <= '$stopdate' 
                                    ORDER BY pr_date");
		$stmt->execute($item);
		return $stmt->fetchall_assoc();
	}      
    /*
     * not finished, not used
     * public static function getAllPurchaseOfItemMatchInvoiceBetweenDates($item, $startdate, $stopdate)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM purchase_order
									LEFT JOIN  purchase_requisition 
		 							ON purchase_order.pr_id = purchase_requisition.pr_id
                                   	INNER JOIN cv_main ON cv_main.cv_id = purchase_requisition.vendor_id
		 							INNER JOIN purchase_items
		 							ON purchase_items.pr_id = purchase_order.po_id
                                	LEFT JOIN invoices_main 
                                   ON purchase_requisition.invoice_id = invoices_main.invoice_id
                                	WHERE purchase_items.inventory_id = :1:
                                	AND pr_date >= '$startdate'
                                    AND pr_date <= '$stopdate' 
                                    ORDER BY pr_date");
		$stmt->execute($item);
		return $stmt->fetchall_assoc();
	}
	*/
	public static function getAllPurchasesGroupByInvoiceBetweenDates($item, $startdate, $stopdate)
	{
		$dbh = new DB_Mysql();
		/*$stmt = $dbh->prepare("SELECT *, GROUP_CONCAT(purchase_requisition.pr_id) AS pr_string,
									  SUM(`purchaseitem_price_total`) AS purchase_total
		                         FROM purchase_requisition
						   INNER JOIN purchase_order 
		 						   ON purchase_order.pr_id = purchase_requisition.pr_id
                           INNER JOIN cv_main ON cv_main.cv_id = purchase_requisition.vendor_id
		 		           INNER JOIN purchase_items
		 						   ON purchase_items.pr_id = purchase_requisition.pr_id
                            LEFT JOIN invoices_main 
                                   ON purchase_requisition.invoice_id = invoices_main.invoice_id
                            LEFT JOIN invoices_items 
                                   ON invoices_items.invoice_id = invoices_main.invoice_id
                                WHERE purchase_items.inventory_id = :1:
                                  AND invoices_items.inventory_id = :1:
                                  AND pr_date >= '$startdate'
                                  AND pr_date <= '$stopdate' 
                             GROUP BY purchase_requisition.invoice_id
                                ORDER BY pr_date");*/
		$stmt = $dbh->prepare("SELECT *, GROUP_CONCAT(purchase_requisition.pr_id) AS pr_string,
									  SUM(`purchaseitem_price_total`) AS purchase_total
		                         FROM purchase_requisition
						   INNER JOIN purchase_order 
		 						   ON purchase_order.pr_id = purchase_requisition.pr_id
                           INNER JOIN cv_main ON cv_main.cv_id = purchase_requisition.vendor_id
		 		           INNER JOIN purchase_items
		 						   ON purchase_items.pr_id = purchase_requisition.pr_id
                            LEFT JOIN (SELECT invoices_items.invoice_id, invoiceitem_price_total FROM invoices_main 
                                   INNER JOIN invoices_items 
                                   		   ON invoices_items.invoice_id = invoices_main.invoice_id
                                        WHERE invoices_items.inventory_id = :1: )  AS inset
                                   ON purchase_requisition.invoice_id = inset.invoice_id
                                WHERE purchase_items.inventory_id = :1:
                                  AND pr_date >= '$startdate'
                                  AND pr_date <= '$stopdate' 
                             GROUP BY purchase_requisition.invoice_id
                             ORDER BY pr_date");
		$stmt->execute($item);
		//echo $stmt->query;
		return $stmt->fetchall_assoc();
	}
	public static function getAllPurchaseReqMatchInvoiceOfItemBetweenDates($item, $startdate, $stopdate)
	{
		$dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM purchase_requisition
						   INNER JOIN cv_main 
						           ON cv_main.cv_id = purchase_requisition.vendor_id
		 				   INNER JOIN purchase_items
		 				           ON purchase_items.pr_id = purchase_requisition.pr_id
                            LEFT JOIN invoices_main 
                                   ON purchase_requisition.invoice_id = invoices_main.invoice_id
                            LEFT JOIN invoices_items 
                                   ON invoices_items.invoice_id = invoices_main.invoice_id
                               	WHERE purchase_items.inventory_id = :1:
                               	AND invoices_items.inventory_id = :1:
                                	AND pr_date >= '$startdate'
                                    AND pr_date <= '$stopdate' 
                                    ORDER BY pr_date");
		$stmt->execute($item);
		return $stmt->fetchall_assoc();
	}
    public static function update_ledger_on_order($ID, $date)
    {
    	$po_info = Purchase_Order::get_purchase_order($ID);
    	if($po_info['purchase_recorded'] != '')
    	{
            throw new PurchaseOrderException("Purchase Already Approved");
    	}
    	if($po_info['vendor_invoices'] == 0)
		{
			// if this vendor is non invoicing
        	//record both the purchase and the invoice recieved on the single transaction
			$dc_line['transaction_dc'] = 'CREDIT';
        	$dc_line['transaction_dc_amount'] = $po_info['po_total'];
        	$dc_line['transaction_account'] = $po_info['vendor_account_id'];
        	$dc_set[] = $dc_line;
	        $dc_line['transaction_dc'] = 'DEBIT';
	        $dc_line['transaction_account'] = $po_info['expense_account_id'];
	        $dc_line['transaction_dc_amount'] = $po_info['po_total'];
        	$dc_set[] = $dc_line;
	        $comment = 'PO #'.$po_info['po_id']." purchased from CV#".$po_info['vendor_id']." - ".$po_info['cv_name'] ;
	        $checkno = '';
	        $transaction_id = transaction::add_transaction('NULL', $date, $comment, $checkno, $dc_set, $override);
	        Purchase_Order::update_purchase_recorded($ID, $transaction_id);
			//this needs to be fixes  vendors will invoice in the future
	        //Purchase_Order::update_invoice_recorded($ID, $transaction_dc['transaction_dc_id']);
	        
		}
		else{
			$dc_line['transaction_dc'] = 'CREDIT';
        	$dc_line['transaction_dc_amount'] = $po_info['po_total'];
        	$dc_line['transaction_account'] = $po_info['clearing_account_id'];
        	$dc_set[] = $dc_line;
	        $dc_line['transaction_dc'] = 'DEBIT';
	        $dc_line['transaction_account'] = $po_info['expense_account_id'];
	        $dc_line['transaction_dc_amount'] = $po_info['po_total'];
        	$dc_set[] = $dc_line;
	        $comment = 'PO #'.$po_info['po_id']." purchased from CV#".$po_info['vendor_id']." - ".$po_info['cv_name'] ;
	        $checkno = '';
	        $transaction_id = transaction::add_transaction('NULL', $date, $comment, $checkno, $dc_set, $override);
	        Purchase_Order::update_purchase_recorded($ID, $transaction_id);
		}
    }
}

?>
