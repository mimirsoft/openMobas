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
require_once("Transaction.class.php");
require_once("Cash_Receipt.class.php");

class CashDisbursementException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}

class Cash_Disbursements{

    public static function create_disbursement($amount, $date, $cv_id, $disbursement_acount, $comment, $check_no, $user_id, $credit_or_clearing=0)
    {
        //record the remittance, then update the legder
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO cash_disbursements
                                       SET total_amount=:1:,
                                           cd_date=:2:,
                                           vendor_id=:3:,
                                           comment=:4:,
                                           checkno=:5:,
                                           disbursement_account=:6:,
                                           employee=:7:,
                                           credit_or_clearing=:8:");
        $stmt->execute($amount, $date, $cv_id, $comment, $check_no, $disbursement_acount, $user_id, $credit_or_clearing);
        $remit_no = mysql_insert_id();
        Cash_Disbursements::update_gl_entry($remit_no, $cv_id);
        return $remit_no;
    }

    public static function update_gl_entry($remit_no)
    {
        $remit_info = Cash_Disbursements::get_disbursement($remit_no);
        if($remit_info['gl_entry'] != NULL)
        {
            throw new Error();
        }
        $override = '';
        $customer_info = CV_Main::get_cv_from_id($remit_info['vendor_id']);
        $dc_line['transaction_dc'] = 'CREDIT';
        $dc_line['transaction_dc_amount'] = $remit_info['total_amount'];
        $dc_line['transaction_account'] = $remit_info['disbursement_account'];
        $dc_set[] = $dc_line;
        $dc_line['transaction_dc'] = 'DEBIT';
        $dc_line['transaction_dc_amount'] = $remit_info['total_amount'];
        $dc_line['transaction_account'] = $customer_info['gl_account_payable'];
        if($customer_info['gl_account_payable'] == '')
        {
            throw new CashDisbursementException("GL Account Payable for Customer CV#".$customer_info['cv_id']."is not set.");
        }
        $dc_set[] = $dc_line;
        $comment2 = "DISBURSEMENT TO CV#".$customer_info['cv_id']." - ".$customer_info['cv_name']." ".$remit_info['comment'] ;
        $transaction_id = transaction::add_transaction('NULL', $remit_info['cd_date'], $comment2, $remit_info['checkno'], $dc_set, $override);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_disbursements 
                                  SET gl_entry=$transaction_id
                                WHERE cd_no=:1:");
        $stmt->execute($remit_no);
        return mysql_insert_id();
    }

    public static function update_disbursement_account($remit_no, $remittance_acount)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_disbursements 
                                  SET disbursement_account=:2:
                                WHERE cd_no=:1:");
        $stmt->execute($remit_no, $remittance_acount);
        return;
    }
    public static function delete_disbursement($remit_no)
    {
        $remit_info = Cash_Disbursements::get_disbursement($remit_no);
        if($remit_info['gl_entry'] != NULL)
        {
            try{
                transaction::delete_transaction($remit_info['gl_entry']);
            }
            catch(TransactionException $exception)
            {
                throw new CashDisbursementException("Cannot delete disbursement, deletion of matching GL entry not allowed by general ledger.");
            }
        }
        Cash_Disbursements::unapply_disbursement($remit_no);
        Cash_Disbursements::unapply_refund($remit_no);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM cash_disbursements 
                                WHERE cd_no=:1:");
        $stmt->execute($remit_no);
        return;
    }
    public static function apply_disbursement($cd_no, $po_id, $amount_applied)
    {
        $amount_applied = floatval($amount_applied);
    	$cd_info = Cash_Disbursements::get_disbursement($cd_no);
        $po_info = Purchase_Order::get_purchase_order($po_id);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(amount_applied) AS total_applied 
                                 FROM payment_relations
                                WHERE cd_no=:1:");
        $stmt->execute($cd_no);
        $row = $stmt->fetch_assoc();
        $remaining_credit = $cd_info['total_amount']-$row['total_applied'];

        //check the remit, is it applied completely?
        if($row['total_applied'] == $cd_info['total_amount'])
        {
            throw new CashDisbursementException("Cannot apply disbursement to purchase_order, disbursement already fully applied");
        }
        //does the remittance have sufficient remaining credit to apply?
        if(bccomp($remaining_credit, $amount_applied, 2) < 0 )
        {
            throw new CashDisbursementException("Cannot apply disbursement to purchase_order, amount attempting to apply ".$amount_applied." greater then remaining disbursement ".$remaining_credit);
        }


        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(amount_applied) AS total_applied 
                                 FROM payment_relations
                                WHERE po_id=:1:");
        $stmt->execute($po_id);
        $row2 = $stmt->fetch_assoc();

        $remaining_balance = $po_info['po_total'] - $row2['total_applied'];
        if($remaining_balance < 0)
        {
            throw new CashReceiptsException("MAJOR ERROR - INVOICE HAS MORE DISBURSEMENT APPLIED THEN PO TOTAL");

        }
        //if the remaining balance owed is less than the amount we are attempting to apply, apply the lesser amount instead
        if($remaining_balance < $amount_applied)
        {
            $amount_applied = $remaining_balance;
        }
        //add the remittance advice
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO payment_relations 
                                       SET cd_no=:1:,
                                           po_id=:2:,
                                           amount_applied=:3:");
        $stmt->execute($cd_no, $po_id, $amount_applied);
        //now update the remittance, and the invoice
        Cash_Disbursements::update_total_applied($cd_no);
        Purchase_Order::update_total_disbursed($po_id);
        return;
    }

		
		
			
	public static function apply_refund($cd_no, $remit_no, $amount_applied)
    {
        $amount_applied = floatval($amount_applied);
    	$cd_info = Cash_Disbursements::get_disbursement($cd_no);
        $remit_info = Cash_Receipts::get_remittance($remit_no);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT v1.total+v2.total AS total_applied FROM
								(SELECT COALESCE(SUM(payment_relations.amount_applied),0) AS total FROM payment_relations WHERE cd_no=:1:) AS v1,
								(SELECT COALESCE(SUM(refund_advice.amount_applied),0) AS total FROM refund_advice WHERE cd_no=:1:) AS v2;");
        $stmt->execute($cd_no);
        $row = $stmt->fetch_assoc();
        $remaining_credit = $cd_info['total_amount']-$row['total_applied'];

        //check the remit, is it applied completely?
        if($row['total_applied'] == $cd_info['total_amount'])
        {
            throw new CashDisbursementException("Cannot apply disbursement to remittance, disbursement already fully applied");
        }
        //does the remittance have sufficient remaining credit to apply?
        if(bccomp($remaining_credit, $amount_applied, 2) < 0 )
        {
            throw new CashDisbursementException("Cannot apply disbursement to remittance, amount attempting to apply ".$amount_applied." greater then remaining disbursement ".$remaining_credit);
        }


        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT v1.total+v2.total AS total_applied FROM
								(SELECT COALESCE(SUM(remittance_advice.amount_applied),0) AS total FROM remittance_advice WHERE remit_no=:1:) AS v1,
								(SELECT COALESCE(SUM(refund_advice.amount_applied),0) AS total FROM refund_advice WHERE remit_no=:1:) AS v2;");
        $stmt->execute($remit_no);
        $row2 = $stmt->fetch_assoc();

        $remaining_balance = $remit_info['total_received'] - $row2['total_applied'];
        if($remaining_balance < 0)
        {
            throw new CashReceiptsException("MAJOR ERROR - REMITTANCE HAS MORE DISBURSEMENT APPLIED THEN REMIT TOTAL");

        }
        //if the remaining balance owed is less than the amount we are attempting to apply, apply the lesser amount instead
        if($remaining_balance < $amount_applied)
        {
            $amount_applied = $remaining_balance;
        }
        //add the remittance advice
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO refund_advice 
                                       SET cd_no=:1:,
                                           remit_no=:2:,
                                           amount_applied=:3:");
        $stmt->execute($cd_no, $remit_no, $amount_applied);
        //now update the remittance, and the invoice
        Cash_Disbursements::update_total_applied($cd_no);
        Cash_Receipts::update_total_applied($remit_no);
        return;
    }
    public static function unapply_disbursement($cd_no)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                 FROM payment_relations
                                WHERE cd_no=:1:");
        $stmt->execute($cd_no);
        $row = $stmt->fetchall_assoc();
        //get all invoices
        //now delete all current advice
        $stmt = $dbh->prepare("DELETE 
                                 FROM payment_relations
                                WHERE cd_no=:1:");
        $stmt->execute($cd_no);
        foreach($row as $r)
        {
            Purchase_Order::update_total_disbursed($r['po_id']);
        }
        Cash_Disbursements::update_total_applied($cd_no);
        return;
    }
    public static function unapply_refund($cd_no)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                 FROM refund_advice
                                WHERE cd_no=:1:");
        $stmt->execute($cd_no);
        $row = $stmt->fetchall_assoc();
        //get all invoices
        //now delete all current advice
        $stmt = $dbh->prepare("DELETE 
                                 FROM refund_advice
                                WHERE cd_no=:1:");
        $stmt->execute($cd_no);
        //update the Cash Receipts that had been set again this Disbursement
        foreach($row as $r)
        {
            Cash_Receipts::update_total_applied($r['remit_no']);
        }
        Cash_Disbursements::update_total_applied($cd_no);
        return;
    }
    public static function update_total_applied($remit_no)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_disbursements 
                                  SET total_applied=(SELECT COALESCE(SUM(amount_applied),0)
                                 					   FROM payment_relations
                                					  WHERE cd_no=:1:)+(SELECT COALESCE(SUM(amount_applied),0)
                                 					   FROM refund_advice
                                					  WHERE cd_no=:1:)
                                WHERE cd_no=:1:");
        $stmt->execute($remit_no);
        return;
    }
    public static function updateIsRefund($cd_no, $is_refund=0)
    {
        if($is_refund == 0)
        {
            //delete all refund_advice for this disbursement
            Cash_Disbursements::unapply_refund($cd_no);
        }
        elseif($is_refund ==1)
        {
            //delete all disbursement advice instead
            Cash_Disbursements::unapply_disbursement($cd_no);
            
        }
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_disbursements 
                                  SET is_refund=:2:
                                WHERE cd_no=:1:");
        $stmt->execute($cd_no, $is_refund);
        return;
    }    
    public static function update_credit_or_clearing($remit_no, $credit_or_clearing)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_disbursements 
                                  SET credit_or_clearing=:2:
                                WHERE cd_no=:1:");
        $stmt->execute($remit_no, $credit_or_clearing);
        return;
    }    
    public static function delete_payment_relations_for_po($po_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM payment_relations WHERE po_id=:1:");
        $stmt->execute($po_id);
        return;
    }    
    public static function update_cd_date($remit_no, $date)
    {
        $remit_info = Cash_Disbursements::get_disbursement($remit_no);
        //update the GL for this disbursement
        try{
            transaction::update_date($remit_info['gl_entry'], $date);
        }
        catch(TransactionException $exception)
        {
            throw new CashDisbursementException("Cannot change date of dibursement, date not allowed by general ledger.");
        }
        //update the disbursement
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_disbursements 
                                  SET cd_date=:2:
                                WHERE cd_no=:1:");
        $stmt->execute($remit_no, $date);
        return;
    }
    public static function update_checkno($remit_no, $checkno)
    {
        $remit_info = Cash_Disbursements::get_disbursement($remit_no);
        //update the GL for this disbursement
        try{
            transaction::update_checkno($remit_info['gl_entry'], $checkno);
        }
        catch(TransactionException $exception)
        {
            throw new CashDisbursementException("Cannot change checkno of dibursement, change not allowed by general ledger.");
        }
        //update the disbursement
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_disbursements 
                                  SET checkno=:2:
                                WHERE cd_no=:1:");
        $stmt->execute($remit_no, $checkno);
        return;
    }
    public static function update_comment($remit_no, $comment)
    {
        $remit_info = Cash_Disbursements::get_disbursement($remit_no);
        //update the GL for this disbursement
        try{
            transaction::update_comment($remit_info['gl_entry'], "DISBURSEMENT TO CV#".$remit_info['cv_id']." - ".$remit_info['cv_name']." ".$comment);
        }
        catch(TransactionException $exception)
        {
            throw new CashDisbursementException("Cannot change comment of dibursement, change not allowed by general ledger.");
        }
        //update the disbursement
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_disbursements 
                                  SET comment=:2:
                                WHERE cd_no=:1:");
        $stmt->execute($remit_no, $comment);
        return;
    }
    public static function get_disbursement($id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cash_disbursements
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id 
                                WHERE cd_no=:1:");
        $stmt->execute($id);
        return $stmt->fetch_assoc();
    }
    public static function verify_remittance_advice()
    {
        $remittances = Cash_Disbursements::getall_disbursements();
        foreach( $remittances as $row)
        {
            Cash_Disbursements::update_total_applied($row['cd_no']);
        }
    }

    public static function getall_unapplied_disbursements()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_disbursements
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id 
                                WHERE total_amount != total_applied");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_disbursements()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_disbursements
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_disbursements_to_vendor($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_disbursements
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id
                                WHERE vendor_id=:1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_real_disbursements_to_vendor($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_disbursements
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id
                                WHERE vendor_id=:1:
                                AND credit_or_clearing = 0
                                ORDER BY cd_date");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }    
    public static function getall_real_disbursements_to_vendor_between_dates($ID, $start, $stop)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_disbursements
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id
                                WHERE vendor_id=:1:
                                AND credit_or_clearing = 0
                                AND cd_date >= '$start'
                                AND cd_date <= '$stop'
                                ORDER BY cd_date");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }    

    public static function getall_disbursements_against_po($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_disbursements
                           INNER JOIN payment_relations 
                                   ON payment_relations.cd_no = cash_disbursements.cd_no
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id
                                   WHERE payment_relations.po_id=:1:
                                   ORDER BY cd_date");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_real_disbursements_against_po($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_disbursements
                           INNER JOIN payment_relations 
                                   ON payment_relations.cd_no = cash_disbursements.cd_no
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id
                                   WHERE payment_relations.po_id=:1:
                                   AND credit_or_clearing = 0
                                   ORDER BY cd_date");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_disbursements_without_ledger_entry()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_disbursements
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_disbursements.vendor_id 
                                WHERE gl_entry IS NULL");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }


}
?>
