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
require_once("Purchase_Order.class.php");
require_once("Transaction.class.php");

class PurchaseRequestException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}

class Purchase_Requisition{

    public static function create_purchase_request($pr_id, $date, $vendor, $expense_account, $payable, $clearing)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO purchase_requisition
                                        SET pr_id=:1:, 
                                            pr_date=:2:, 
                                            vendor_id=:3:,
                                            expense_account_id=:4:,
                                            vendor_account_id=:5:,
                                            clearing_account_id=:6:");
        $stmt->execute($pr_id, $date, $vendor, $expense_account, $payable, $clearing);
        return mysql_insert_id();

    }
    public static function delete_pr($invoice_id)
    {
        //$invoice = Purchase_Requisition::get_purchase_requisition($invoice_id);
       /* if($invoice['invoice_charged'] != NULL)
        {
            throw new InvoiceException("Cannot delete a charged Invoice, Remove Charge First");
        }
        
        */
        
        $dbh = new DB_Mysql();
        //once it is approved...it cannot be deleted
        $stmt = $dbh->prepare("DELETE FROM purchase_requisition
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();
    }
    public static function update_pr_total($invoice_id)
    {
        $pr_info = Purchase_Requisition::get_purchase_requisition($invoice_id);
		Purchase_Requisition::update_subtotal($invoice_id);
		Purchase_Requisition::update_discount_total($invoice_id);
        $dbh = new DB_Mysql();
        //update the total
        $stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET pr_total=pr_subtotal-pr_discount_total
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id);
        //if there is a PO
        if($pr_info['po_id'] != "")
        {
            //echo $pr_info['po_id']."poid";
            Purchase_Order::update_po_total($pr_info['po_id']);
        }
        return mysql_insert_id();
    }
    public static function approve($pr_id, $user)
    {
    	$pr_info = Purchase_Requisition::get_purchase_requisition($pr_id);
		if($pr_info['approved'] == 1)
    	{
            throw new PurchaseRequestException("Purchase Already Approved");
    	}
    	Purchase_Requisition::update_approved($pr_id, 1);
    	Purchase_Requisition::update_approver($pr_id, $user);
		Purchase_Requisition::update_pr_total($pr_id);
    	//if PO does not exist
    	if($pr_info['po_id'] == "")
		{
		    Purchase_Requisition::generate_purchaseOrder($pr_id);
		}
		else
		{
		    //i don't know why this has to be here, but it does.  Something is unsetting the po_id on the purchase_items tables;
		    Purchase_Requisition::update_purchaseitem_po($pr_id, $pr_info['po_id']);
		    $po_info = Purchase_Order::get_purchase_order($pr_info['po_id']);
    	    Purchase_Order::update_po_total($pr_info['po_id']);
		    //print_r($po_info);
    	    //exit;
		    if($po_info['pr_id'] == $pr_id && $po_info['purchase_gl_entry'] == '')
		    {
		        //update the GL with the transactions
    	        Purchase_Order::update_ledger_on_order($po_info['po_id'], $po_info['po_date']);
		    }
		}
		return $pr_id;

    }
    public static function unapprove($pr_id)
    {
		//delete GL entry for PO
		$po_info = Purchase_Order::get_purchase_order_from_pr($pr_id);
        try{
    		transaction::delete_transaction($po_info['purchase_gl_entry']);
		}
		catch(TransactionException $exception)
		{
			throw new PurchaseRequestException($exception->message);
		}
		//mark as not approved
		Purchase_Requisition::update_approved($pr_id, 0);
    	//delete PO
    	//Purchase_Order::delete_purchase_order($po_info['po_id']);
        return $pr_id;
    }
    public static function is_approved($pr_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT approved 
                            FROM purchase_requisition 
                                WHERE pr_id=:1:");
        $stmt->execute($pr_id);
        $row = $stmt->fetch_assoc();
        return $row['approved'];
    }
    
    public static function update_po_id($invoice_id, $dc)
    {
		$dbh = new DB_Mysql();
    	$stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET po_id=$dc
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();

    }
    public static function update_pr_requestor($invoice_id, $dc)
    {
		$dbh = new DB_Mysql();
    	$stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET pr_requestor=$dc
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();
    }

    public static function update_auto_generated($invoice_id, $dc)
    {
		$dbh = new DB_Mysql();
    	$stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET auto_generated=$dc
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();

    }
    public static function update_approved($invoice_id, $dc)
    {
		$dbh = new DB_Mysql();
    	$stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET approved=$dc
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();

    }
    public static function update_approver($invoice_id, $dc)
    {
		$dbh = new DB_Mysql();
    	$stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET pr_approver=$dc
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();
    }
    public static function update_expense_account($invoice_id, $dc)
    {
		$dbh = new DB_Mysql();
    	$stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET expense_account_id=$dc
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id);
        return mysql_insert_id();

    }
    public static function update_discount($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET pr_discount=:2:
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id, $dc);
        Purchase_Requisition::update_discount_total($invoice_id);
        //now update the total
        Purchase_Requisition::update_pr_total($invoice_id);
        return mysql_insert_id();
    }   
    public static function update_discount_total($pr_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET pr_discount_total=pr_discount*pr_subtotal
                                WHERE pr_id=:1:");
        $stmt->execute($pr_id);
        //now update the total
        return mysql_insert_id();
    }   
    public static function update_subtotal($pr_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT COALESCE(SUM(`purchaseitem_price_total`),0.00) AS total  
                                 FROM purchase_items
                                WHERE pr_id=:1:");
        $stmt->execute($pr_id);
        $row = $stmt->fetch_assoc();
        //update the subtotal
        $stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET pr_subtotal=$row[total]
                                WHERE pr_id=:1:");
        $stmt->execute($pr_id);
    }   
    
    public static function update_description($invoice_id, $dc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET purchase_description=:2:
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id, $dc);
        return mysql_insert_id();

    }    
    public static function update_date($invoice_id, $date)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_requisition 
                                  SET pr_date=:2:
                                WHERE pr_id=:1:");
        $stmt->execute($invoice_id, $date);
        return mysql_insert_id();

    }
    public static function generate_purchaseOrder($pr_id)
    {
        //check for existing PO for this PR
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT COUNT(*) AS num from purchase_order 
                                WHERE pr_id=:1:");
      	$stmt->execute($pr_id);
        $row = $stmt->fetch_assoc();
        if($row['num'] > 0 )
        {
            throw new PurchaseRequestException("PO Already Generated for this PR.  There exists a PO with this PR as index");
        }
    	// generate PO from PR
    	$date = date('Y')."-".date('m')."-".date('d');
    	
    	$pr_info = Purchase_Requisition::get_purchase_requisition($pr_id);
    	$po_id = Purchase_Order::create_purchase_order(NULL, $pr_id, $date, $pr_info['vendor_id'], $pr_info['expense_account_id'], $pr_info['vendor_account_id'], $pr_info['clearing_account_id']);
        Purchase_Order::update_discount($po_id, $pr_info['pr_discount']);
    	Purchase_Requisition::update_po($pr_id, $po_id);
		Purchase_Requisition::update_purchaseitem_po($pr_id, $po_id);
		$po_info = Purchase_Order::get_purchase_order($pr_id);
    	// update the general ledger.
    	Purchase_Order::update_ledger_on_order($po_id, $date);
        //else, record just the purchase and credit the clearing account
        return $po_id;
    
    }

    
    public static function add_pr_item($invoice_id, $cost_per, $count, $inventory_id)
    {
        if(Purchase_Requisition::is_approved($invoice_id) == 1)
        {
            throw new PurchaseRequestException("Cannot Add Items, pr already approved");
        }
        $total = $cost_per*$count;
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO purchase_items
                                       SET purchaseitem_id=NULL, 
                                           pr_id=:1:, 
                                           purchaseitem_price_per=:2:, 
                                           purchaseitem_price_total=$total, 
                                           purchaseitem_count=:3:,
                                           inventory_id=:4:");
        $stmt->execute($invoice_id, $cost_per, $count, $inventory_id);
        Purchase_Requisition::update_pr_total($invoice_id);
        return mysql_insert_id();
    }
    public static function update_invoiceitem_count($invoiceitem_id, $count)
    {
        $invoice_item = Purchase_Requisition::get_purchaseitem($invoiceitem_id);
        if(Purchase_Requisition::is_approved($invoice_item['pr_id']) == 1)
        {
            throw new PurchaseRequestException("Cannot update count, pr already approved");
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_items
                                  SET purchaseitem_count=:2:
                                WHERE purchaseitem_id=:1:");
        $stmt->execute($invoiceitem_id, $count);
        Purchase_Requisition::update_purchaseitem_total($invoiceitem_id);
        
        return mysql_insert_id();

    }

    public static function update_invoiceitem_price_per($invoiceitem_id, $per)
    {
        $invoice_item = Purchase_Requisition::get_purchaseitem($invoiceitem_id);
        if(Purchase_Requisition::is_approved($invoice_item['pr_id']) == 1)
        {
            throw new PurchaseRequestException("Cannot update price per, pr already approved");
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_items
                                  SET purchaseitem_price_per=:2:
                                WHERE purchaseitem_id=:1:");
        $stmt->execute($invoiceitem_id, $per);
        Purchase_Requisition::update_purchaseitem_total($invoiceitem_id);
        return mysql_insert_id();
    }
    
    public static function update_po($pr_id, $po_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_requisition
                                  SET po_id=:2:
                                WHERE pr_id=:1:");
        $stmt->execute($pr_id, $po_id);
    }
    public static function updateInvoiceID($pr_id, $po_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_requisition
                                  SET invoice_id=:2:
                                WHERE pr_id=:1:");
        $stmt->execute($pr_id, $po_id);
    }
    public static function update_purchaseitem_po($pr_id, $po_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_items
                                  SET po_id=:2:
                                WHERE pr_id=:1:");
        $stmt->execute($pr_id, $po_id);
		Purchase_Order::update_po_total($po_id);
    }
    
    public static function update_purchaseitem_total($invoiceitem_id)
    {
        $item = Purchase_Requisition::get_purchaseitem($invoiceitem_id);
        $total = $item['purchaseitem_price_per']*$item['purchaseitem_count'];
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE purchase_items
                                  SET purchaseitem_price_total=:2:
                                WHERE purchaseitem_id=:1:");
        $stmt->execute($invoiceitem_id, $total);
        Purchase_Requisition::update_pr_total($item['pr_id']);
        return mysql_insert_id();
    }

     public static function delete_purchaseitem($invoiceitem_id)
    {
        $invoice_item = Purchase_Requisition::get_purchaseitem($invoiceitem_id);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM purchase_items
                                WHERE purchaseitem_id=:1:");
        $stmt->execute($invoiceitem_id);
        Purchase_Requisition::update_pr_total($invoice_item['pr_id']);
        return mysql_insert_id();
    }

    public static function getall_purchaserequest_items($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM purchase_items 
                           INNER JOIN inventory_items 
                                   ON inventory_items.inventory_id = purchase_items.inventory_id 
                           INNER JOIN inventory_types
                                   ON inventory_items.item_type = inventory_types.inventorytype_id 
                                WHERE pr_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function get_purchaseitem($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM purchase_items 
                           INNER JOIN inventory_items 
                                   ON inventory_items.inventory_id = purchase_items.inventory_id 
                           INNER JOIN inventory_types
                                   ON inventory_items.item_type = inventory_types.inventorytype_id 
                                WHERE purchase_items.purchaseitem_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }

    public static function get_purchase_requisition($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *,  purchase_requisition.vendor_id,  ea.account_fullname AS expenseaccount_fullname,
        							va.account_fullname AS vendoraccount_fullname 
                                 FROM purchase_requisition 
                                 INNER JOIN cv_main
        						ON cv_main.cv_id = purchase_requisition.vendor_id
                                   INNER JOIN transactions_accounts AS ea
                                   ON ea.account_id = purchase_requisition.expense_account_id 
                                   INNER JOIN transactions_accounts AS va
                                   ON va.account_id = purchase_requisition.vendor_account_id 
                                   WHERE purchase_requisition.pr_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    
    public static function getall_requisitions()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_requisition INNER JOIN cv_main ON cv_main.cv_id = purchase_requisition.vendor_id");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_unapprovedrequisitions()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_requisition 
        						INNER JOIN cv_main ON cv_main.cv_id = purchase_requisition.vendor_id 
        						WHERE approved = 0");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_approvedrequisitions_without_po()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, purchase_requisition.pr_id FROM purchase_requisition 
        				   INNER JOIN cv_main 
        				           ON cv_main.cv_id = purchase_requisition.vendor_id 
        				    LEFT JOIN purchase_order ON purchase_order.pr_id = purchase_requisition.pr_id
        				        WHERE pr_approver IS NOT NULL 
        				          AND purchase_order.po_id IS NULL");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_approvedrequisitions()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_requisition INNER JOIN cv_main ON cv_main.cv_id = purchase_requisition.vendor_id WHERE pr_approver IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_approvedrequisitions_with_po()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM purchase_requisition 
        				   INNER JOIN cv_main 
        				           ON cv_main.cv_id = purchase_requisition.vendor_id 
        				    LEFT JOIN purchase_order ON purchase_order.pr_id = purchase_requisition.pr_id
        				        WHERE pr_approver IS NOT NULL 
        				          AND purchase_order.po_id IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function unapplyDisbursementsAgainstPR($pr_id)
    {
        $po_info = Purchase_Order::get_purchase_order_from_pr($pr_id);
        $row = Cash_Disbursements::getall_disbursements_against_po($po_info['po_id']);
        //get all remits
        //now delete all current advice
        Cash_Disbursements::delete_payment_relations_for_po($po_info['po_id']);
        //no update all those disbursements
        foreach($row as $r)
        {
        	Cash_Disbursements::update_total_applied($r['cd_no']);
        }
        //now update the total remitted, and the paid in full flagg
        Purchase_Order::update_total_disbursed($po_info['po_id']);
        return;
    }
}

?>
