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
require_once("Invoice.class.php");

class CashReceiptsException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}

class Cash_Receipts{

    public static function create_remittance($amount, $date, $cv_id, $remittance_acount, $comment, $check_no, $user_id, $credit_or_clearing=0)
    {
        //record the remittance, then update the legder
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO cash_receipts
                                       SET total_received=:1:,
                                           remit_date=:2:,
                                           customer_id=:3:,
                                           comment=:4:,
                                           checkno=:5:,
                                           remittance_account=:6:,
                                           employee=:7:,
                                           credit_or_clearing=:8:");
        $stmt->execute($amount, $date, $cv_id, $comment, $check_no, $remittance_acount, $user_id, $credit_or_clearing);
        $remit_no = mysql_insert_id();
        Cash_Receipts::update_gl_entry($remit_no, $cv_id);
        return $remit_no;
    }

    public static function update_gl_entry($remit_no)
    {
        $remit_info = Cash_Receipts::get_remittance($remit_no);
        if($remit_info['gl_entry'] != NULL)
        {
            throw new Error();
        }
        $override = '';
        $customer_info = CV_Main::get_cv_from_id($remit_info['customer_id']);
        $dc_line['transaction_dc'] = 'CREDIT';
        $dc_line['transaction_dc_amount'] = $remit_info['total_received'];
        $dc_line['transaction_account'] = $customer_info['gl_account_receivable'];
        $dc_set[] = $dc_line;
        $dc_line['transaction_dc'] = 'DEBIT';
        $dc_line['transaction_dc_amount'] = $remit_info['total_received'];
        $dc_line['transaction_account'] = $remit_info['remittance_account'];
        $dc_set[] = $dc_line;
        $comment2 = "CHECK FROM CV#".$customer_info['cv_id']." - ".$customer_info['cv_name']." ".$remit_info['comment'] ;
        $transaction_id = transaction::add_transaction('NULL', $remit_info['remit_date'], $comment2, $remit_info['checkno'], $dc_set, $override);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_receipts 
                                  SET gl_entry=$transaction_id
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no);
        return mysql_insert_id();
    }

    public static function update_remittance_account($remit_no, $remittance_acount)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_receipts 
                                  SET remittance_account=$remittance_acount
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no);
        return;
    }
    public static function delete_remittance($remit_no)
    {
        $remit_info = Cash_Receipts::get_remittance($remit_no);
        if($remit_info['gl_entry'] != NULL)
        {
            try{
                transaction::delete_transaction($remit_info['gl_entry']);
            }
            catch(TransactionException $exception)
            {
                throw new CashReceiptsException("Cannot remittance, delete of GL entry for remittance not allowed by general ledger.");
            }
        }
        Cash_Receipts::unapply_remittance($remit_no);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM cash_receipts 
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no);
        return;
    }
    public static function deleteMatchingClearingRemittance($amount, $cv_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                 FROM cash_receipts
                                WHERE total_received=:1:
                                  AND customer_id=:2:
                                  AND credit_or_clearing = 1
                                LIMIT 1");
        $stmt->execute($amount, $cv_id);
        $row = $stmt->fetch_assoc();
        Cash_Receipts::delete_remittance($row['remit_no']);
        return;
    }
    public static function apply_remittance($remit_no, $invoice_id, $amount_applied)
    {
        $remit_info = Cash_Receipts::get_remittance($remit_no);
        $invoice_info = Invoice::get_invoice($invoice_id);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT v1.total+v2.total AS total_applied FROM
								(SELECT COALESCE(SUM(remittance_advice.amount_applied),0) AS total FROM remittance_advice WHERE remit_no=:1:) AS v1,
								(SELECT COALESCE(SUM(refund_advice.amount_applied),0) AS total FROM refund_advice WHERE remit_no=:1:) AS v2;");
        $stmt->execute($remit_no);
        $row = $stmt->fetch_assoc();
        $remaining_credit = $remit_info['total_received']-$row['total_applied'];

        //check the remit, is it applied completely?
        if($row['total_applied'] == $remit_info['total_received'])
        {
            throw new CashReceiptsException("Cannot apply remittance to invoice, remittance already fully applied");
        }
        //does the remittance have sufficient remaining credit to apply?
        if(bccomp($remaining_credit, $amount_applied, 2) < 0 )
        {
            throw new CashReceiptsException("Cannot apply remittance to invoice, amount attempting to apply greater then remaining credit");
        }


        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT SUM(amount_applied) AS total_applied 
                                 FROM remittance_advice
                                WHERE invoice_id=:1:");
        $stmt->execute($invoice_id);
        $row2 = $stmt->fetch_assoc();

        $remaining_balance = $invoice_info['invoice_total'] - $row2['total_applied'];
        if($remaining_balance < 0)
        {
            throw new CashReceiptsException("MAJOR ERROR - INVOICE HAS MORE REMIITANCE APPLIED THEN INVOICE TOTAL");

        }
        //if the remaining balance owed is less than the amount we are attempting to apply, apply the lesser amount instead
        if($remaining_balance < $amount_applied)
        {
            $amount_applied = $remaining_balance;
        }
        //add the remittance advice
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO remittance_advice 
                                       SET remit_no=:1:,
                                           invoice_id=:2:,
                                           amount_applied=:3:");
        $stmt->execute($remit_no, $invoice_id, $amount_applied);
        //now update the remittance, and the invoice
        Cash_Receipts::update_total_applied($remit_no);
        Invoice::update_total_remitted($invoice_id);
        return;
    }

    public static function unapply_remittance($remit_no)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                 FROM remittance_advice
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no);
        $row = $stmt->fetchall_assoc();
        //get all invoices
        //now delete all current advice
        $stmt = $dbh->prepare("DELETE 
                                 FROM remittance_advice
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no);
        foreach($row as $r)
        {
        	Invoice::update_total_remitted($r['invoice_id']);
        }
        $stmt = $dbh->prepare("SELECT *
                                 FROM refund_advice
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no);
        $row = $stmt->fetchall_assoc();
        //get all invoices
        //now delete all current advice
        $stmt = $dbh->prepare("DELETE 
                                 FROM refund_advice
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no);
        foreach($row as $r)
        {
            Cash_Disbursements::update_total_applied($r['cd_no']);
        }
        Cash_Receipts::update_total_applied($remit_no);
       
        
        return;
    }

    
    public static function update_total_applied($remit_no)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_receipts 
                                  SET total_applied=(SELECT COALESCE(SUM(amount_applied),0)
                                 					   FROM remittance_advice
                                					  WHERE remit_no=:1:)+(SELECT COALESCE(SUM(amount_applied),0)
                                 					   FROM refund_advice
                                					  WHERE remit_no=:1:)
                                WHERE remit_no=:1:");
        
        $stmt->execute($remit_no);
        return;
    }
    public static function get_remittance($id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cash_receipts
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_receipts.customer_id 
                                WHERE remit_no=:1:");
        $stmt->execute($id);
        return $stmt->fetch_assoc();
    }
    public static function verify_remittance_advice()
    {
        $remittances = Cash_Receipts::getall_remittances();
        foreach( $remittances as $row)
        {
            Cash_Receipts::update_total_applied($row['remit_no']);
        }
    }

    public static function getall_unapplied_remittances()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_receipts
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_receipts.customer_id 
                                WHERE total_received != total_applied");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_remittances()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_receipts
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_receipts.customer_id");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_remittances_against_inv($inv)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_receipts
                           INNER JOIN remittance_advice 
                                   ON remittance_advice.remit_no = cash_receipts.remit_no
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_receipts.customer_id
                                WHERE remittance_advice.invoice_id=:1:
                                ORDER BY remit_date");
        $stmt->execute($inv);
        return $stmt->fetchall_assoc();
    }
    public static function getall_remittances_of_customer($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_receipts
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_receipts.customer_id
                                   WHERE customer_id=:1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getAllUnappliedRemittancesOfCustomer($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_receipts
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_receipts.customer_id
                                WHERE customer_id=:1:
                                  AND total_received != total_applied");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_real_remittances_of_customer_between_dates($ID, $start, $stop)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_receipts
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_receipts.customer_id
                                WHERE customer_id=:1:
                                AND credit_or_clearing = 0
                                AND remit_date >= '$start'
                                AND remit_date <= '$stop'
                                ORDER BY remit_date");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }    
    public static function getall_remittances_without_ledger_entry()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM cash_receipts
                           INNER JOIN cv_main 
                                   ON cv_main.cv_id = cash_receipts.customer_id 
                                WHERE gl_entry IS NULL");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function update_credit_or_clearing($remit_no, $credit_or_clearing)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_receipts 
                                  SET credit_or_clearing=:2:
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no, $credit_or_clearing);
        return;
    }    
    public static function update_remit_date($remit_no, $date)
    {
        $remit_info = Cash_Receipts::get_remittance($remit_no);
        //update the GL for this disbursement
        try{
            transaction::update_date($remit_info['gl_entry'], $date);
        }
        catch(TransactionException $exception)
        {
            throw new CashReceiptsException("Cannot change date of receipt, date not allowed by general ledger.");
        }
        //update the disbursement
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_receipts 
                                  SET remit_date=:2:
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no, $date);
        return;
    }
    public static function update_checkno($remit_no, $checkno)
    {
        $remit_info = Cash_Receipts::get_remittance($remit_no);
        //update the GL for this disbursement
        try{
            transaction::update_checkno($remit_info['gl_entry'], $checkno);
        }
        catch(TransactionException $exception)
        {
            throw new CashReceiptsException("Cannot change checkno of receipt, change not allowed by general ledger.");
        }
        //update the disbursement
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_receipts 
                                  SET checkno=:2:
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no, $checkno);
        return;
    }
    public static function update_comment($remit_no, $checkno)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cash_receipts 
                                  SET comment=:2:
                                WHERE remit_no=:1:");
        $stmt->execute($remit_no, $checkno);
        return;
    }
}
?>
