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
class CVException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}
class CV_Main{
    
    public static function create_cv_object($cv_id, $name, $number, $taxid, $is_customer, $is_vendor, $address, $city, $state, $zip, $email, $phone)
    {
        $dbh = new DB_Mysql();
        if($cv_id == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO cv_main 
                                           SET cv_id=:1:, 
                                               cv_name=:2:, 
                                               cv_number=:3:, 
                                               tax_id=:4:, 
                                               is_customer=:5:, 
                                               is_vendor=:6:,
                                               cv_default_address=:7:,
                                               cv_default_city=:8:,
                                               cv_default_state=:9:,
                                               cv_default_zip=:10:,
                                          cv_default_email=:11:,
                                          cv_default_phone=:12:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE cv_main 
                                      SET cv_name=:2:, 
                                          cv_number=:3:, 
                                          tax_id=:4:, 
                                          is_customer=:5:, 
                                          is_vendor=:6:,
                                          cv_default_address=:7:,
                                          cv_default_city=:8:,
                                          cv_default_state=:9:,
                                          cv_default_zip=:10:,
                                          cv_default_email=:11:,
                                          cv_default_phone=:12:
                                    WHERE cv_id=:1:");
        }
        $stmt->execute($cv_id, $name, $number, $taxid, $is_customer, $is_vendor, $address, $city, $state, $zip, $email, $phone);
        return $stmt->dbh->insert_id;

    }
    
    public static function delete_cv_object($cv_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM cv_main WHERE cv_id=:1:  ");
        $stmt->execute($cv_id);
    }
    public static function update_clear_with_vendor($ID, $clear)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET clear_with_vendor = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $clear);
    }
    public static function update_clear_with_customer($ID, $clear)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET clear_with_customer = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $clear);
    }
    public static function update_orders_accepted($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET orders_accepted = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function update_cv_notes($ID, $notes)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET cv_notes = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $notes);
    }
    public static function update_receivable_total($ID, $total)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET receivable_invoices_total = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $total);
    }
    public static function update_payments_accepted($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET payments_accepted = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function updatePaymentsAcceptedNote($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET payments_accepted_note = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function update_default_careof($ID, $default)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET cv_default_careof = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $default);
    }
    public static function update_purchases_allowed($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET purchases_allowed = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function update_disbursements_allowed($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET disbursements_allowed = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function update_default_statement_type($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET cv_default_statement_type = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function update_default_invoice_type($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET cv_default_invoice_type = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function update_default_payment_type($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET cv_default_payment_type = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function update_is_closed($ID, $allowed)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main SET is_closed = :2: WHERE cv_id=:1:");
        $stmt->execute($ID, $allowed);
    }
    public static function add_tag($cv_category_id, $cv_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO cv_tags 
                                       SET cv_category_id=:1:, 
                                           cv_id=:2: ");
        try{
             $stmt->execute($cv_category_id, $cv_id);
        }
        catch(MysqlException $exception)
        {
            throw new CVException("CV ".$cv_id." already tagged with category ".$cv_category_id."<BR/>");
        }

    }
	public static function delete_tag($leadcat_id, $leadID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM cv_tags 
                                       WHERE cv_category_id=:1:
                                       AND 
                                           cv_id=:2: ");

        $stmt->execute($leadcat_id, $leadID);
    }   
    public static function get_tags_of_cv($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_tags 
        				   INNER JOIN cv_category 
        				   ON cv_category.cv_category_id = cv_tags.cv_category_id 
        				   WHERE cv_tags.cv_id=:1: ORDER BY cv_category_name");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }   
    public static function getall_customers_with_accounts()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_main 
                                WHERE is_customer=1 
                                  AND orders_accepted = 1
                             ORDER BY cv_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function search_cv($name, $tag)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, GL_AR.account_fullname AS GL_AR_fullname, GL_AR.account_id AS GL_AR_id,
                                      GL_AP.account_fullname AS GL_AP_fullname, GL_AP.account_id AS GL_AP_id, 
                                      cv_main.cv_id,
                                       GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                                 FROM cv_main 
                            LEFT JOIN cv_tags
                                   ON cv_main.cv_id = cv_tags.cv_id 
                            LEFT JOIN transactions_accounts AS GL_AR 
                                   ON cv_main.gl_account_receivable = GL_AR.account_id
                            LEFT JOIN transactions_accounts AS GL_AP 
                                   ON cv_main.gl_account_payable = GL_AP.account_id
                                WHERE cv_name LIKE '%$name%'
                                  AND cv_tags.cv_category_id = :1: 
                             GROUP BY cv_main.cv_id 
                             ORDER BY cv_name");
        $stmt->execute($tag);
        return $stmt->fetchall_assoc();
    }
    
    public static function search_vendors_by_name($string)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, GL_AR.account_fullname AS GL_AR_fullname, GL_AR.account_id AS GL_AR_id,
                                      GL_AP.account_fullname AS GL_AP_fullname, GL_AP.account_id AS GL_AP_id, 
                                      cv_main.cv_id,
                            GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                           FROM cv_main 
                            LEFT JOIN cv_tags
                                  ON  cv_main.cv_id = cv_tags.cv_id 
                            LEFT JOIN transactions_accounts AS GL_AR 
                                   ON cv_main.gl_account_receivable = GL_AR.account_id
                            LEFT JOIN transactions_accounts AS GL_AP 
                                   ON cv_main.gl_account_payable = GL_AP.account_id
                                WHERE is_vendor=1 
                                  AND cv_name LIKE '%$string%' 
                                GROUP BY cv_main.cv_id 
                                   ORDER BY cv_name");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function search_customers_by_name($string)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, GL_AR.account_fullname AS GL_AR_fullname, GL_AR.account_id AS GL_AR_id,
                                      GL_AP.account_fullname AS GL_AP_fullname, GL_AP.account_id AS GL_AP_id,
                                      GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string, cv_main.cv_id,
                            GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                           FROM cv_main 
                            LEFT JOIN cv_tags
                                ON  cv_main.cv_id = cv_tags.cv_id 
                            LEFT JOIN transactions_accounts AS GL_AR 
                                   ON cv_main.gl_account_receivable = GL_AR.account_id
                            LEFT JOIN transactions_accounts AS GL_AP 
                                   ON cv_main.gl_account_payable = GL_AP.account_id
                                WHERE is_customer=1 
                                  AND cv_name LIKE '%$string%' 
                                GROUP BY cv_main.cv_id 
                                   ORDER BY cv_name");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function search_neither_by_name($string)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, GL_AR.account_fullname AS GL_AR_fullname, GL_AR.account_id AS GL_AR_id,
                                      GL_AP.account_fullname AS GL_AP_fullname, GL_AP.account_id AS GL_AP_id, cv_main.cv_id,
                            GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                           FROM cv_main 
                            LEFT JOIN cv_tags
                        ON  cv_main.cv_id = cv_tags.cv_id 
                            LEFT JOIN transactions_accounts AS GL_AR 
                                   ON cv_main.gl_account_receivable = GL_AR.account_id
                            LEFT JOIN transactions_accounts AS GL_AP 
                                   ON cv_main.gl_account_payable = GL_AP.account_id
                                WHERE is_vendor=0 AND is_customer=0
                                AND cv_name LIKE '%$string%' 
                                GROUP BY cv_main.cv_id 
                                   ORDER BY cv_name");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function search_cv_by_name($string)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, GL_AR.account_fullname AS GL_AR_fullname, GL_AR.account_id AS GL_AR_id,
                                      GL_AP.account_fullname AS GL_AP_fullname, GL_AP.account_id AS GL_AP_id, cv_main.cv_id,
                            GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                           FROM cv_main 
                            LEFT JOIN cv_tags
                        ON  cv_main.cv_id = cv_tags.cv_id 
                            LEFT JOIN transactions_accounts AS GL_AR 
                                   ON cv_main.gl_account_receivable = GL_AR.account_id
                            LEFT JOIN transactions_accounts AS GL_AP 
                                   ON cv_main.gl_account_payable = GL_AP.account_id
                                WHERE cv_name LIKE '%$string%' 
                                GROUP BY cv_main.cv_id 
                                   ORDER BY cv_name");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_cv()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, cv_main.cv_id,
                            GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                           FROM cv_main 
                            LEFT JOIN cv_tags
                        ON  cv_main.cv_id = cv_tags.cv_id 
                        GROUP BY cv_main.cv_id 
                        ORDER BY cv_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_customers()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, cv_main.cv_id,
                            GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                           FROM cv_main 
                            LEFT JOIN cv_tags
                        ON  cv_main.cv_id = cv_tags.cv_id 
                    WHERE is_customer=1     
                    GROUP BY cv_main.cv_id 
        ORDER BY cv_name ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_vendors_with_tag($name)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_main 
                                INNER JOIN cv_tags
                                ON cv_tags.cv_id = cv_main.cv_id 
                                INNER JOIN cv_category 
                                ON cv_category.cv_category_id = cv_tags.cv_category_id 
                                WHERE is_vendor=1 
                                AND cv_category_name = :1:
                                    ORDER BY cv_name  ");
        $stmt->execute($name);
        return $stmt->fetchall_assoc();
    }    
    public static function getall_with_tag($id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_main 
                                INNER JOIN cv_tags
                                ON cv_tags.cv_id = cv_main.cv_id 
                                INNER JOIN cv_category 
                                ON cv_category.cv_category_id = cv_tags.cv_category_id 
                                WHERE cv_tags.cv_category_id = :1:
                                    ORDER BY cv_name  ");
        $stmt->execute($id);
        return $stmt->fetchall_assoc();
    }    
    public static function getall_with_tag_name($name)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_main 
                                INNER JOIN cv_tags
                                ON cv_tags.cv_id = cv_main.cv_id 
                                INNER JOIN cv_category 
                                ON cv_category.cv_category_id = cv_tags.cv_category_id 
                                WHERE cv_category_name = :1:
                                    ORDER BY cv_name  ");
        $stmt->execute($name);
        return $stmt->fetchall_assoc();
    }    
    public static function getall_vendors()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, cv_main.cv_id,
                            GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                           FROM cv_main 
                            LEFT JOIN cv_tags
                        ON  cv_main.cv_id = cv_tags.cv_id 
                                  LEFT JOIN transactions_accounts AS GL_AP 
                                   ON cv_main.gl_account_payable = GL_AP.account_id
                                   WHERE is_vendor=1            
                                      GROUP BY cv_main.cv_id 
                                    ORDER BY cv_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
   public static function getall_vendors_with_items()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_main INNER JOIN inventory_items ON cv_main.cv_id = inventory_items.cv_id WHERE is_vendor=1 GROUP BY cv_main.cv_id ORDER BY cv_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
   public static function getall_vendors_with_items_purchases_allowed()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_main INNER JOIN inventory_items ON cv_main.cv_id = inventory_items.cv_id WHERE is_vendor=1 AND purchases_allowed = 1 GROUP BY cv_main.cv_id ORDER BY cv_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_vendors_per_items()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, cv_main.cv_id FROM cv_main LEFT JOIN inventory_items ON cv_main.cv_id = inventory_items.cv_id WHERE is_vendor=1 ORDER BY cv_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    
    public static function getall_neither()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, cv_main.cv_id,
                            GROUP_CONCAT(cv_tags.cv_category_id) AS cv_tag_string
                           FROM cv_main 
                            LEFT JOIN cv_tags
                        ON  cv_main.cv_id = cv_tags.cv_id 
                        WHERE is_vendor=0 AND is_customer=0 GROUP BY cv_main.cv_id 
                        ORDER BY cv_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function get_cv_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, GL_AR.account_fullname AS GL_AR_fullname, GL_AR.account_name AS GL_AR_name, GL_AR.account_id AS GL_AR_id,
                                      GL_AP.account_fullname AS GL_AP_fullname, GL_AP.account_name AS GL_AP_name, GL_AP.account_id AS GL_AP_id
                                 FROM cv_main 
                            LEFT JOIN transactions_accounts AS GL_AR 
                                   ON cv_main.gl_account_receivable = GL_AR.account_id
                            LEFT JOIN transactions_accounts AS GL_AP 
                                   ON cv_main.gl_account_payable = GL_AP.account_id
                            WHERE cv_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function getall_cv_contacts_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_contacts INNER JOIN contacts_main ON contacts_main.contacts_id = cv_contacts.contacts_id WHERE cv_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_cv_users($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_mtm_users INNER JOIN user_main ON cv_mtm_users.user_id = user_main.user_id WHERE cv_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_cv_for_user($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_mtm_users INNER JOIN user_main ON cv_mtm_users.user_id = user_main.user_id WHERE user_main.user_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function delete_cv_contacts_by_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM cv_contacts WHERE cv_contacts_id = :1:");
        $stmt->execute($ID);
    }
   public static function attach_contact($cv_contact_id, $cv_id, $contacts_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_contacts WHERE cv_id=:1: AND contacts_id=:2: ");

        $stmt->execute($cv_id, $contacts_id);
        if($stmt->num_rows() > 0)
        {
            throw new Exception("CV Object $cv_id already has contact $contacts_id attached.");
        }
        $stmt = $dbh->prepare("INSERT INTO `cv_contacts` 
                                           SET cv_contacts_id=:1:, 
                                               cv_id=:2:, 
                                               contacts_id=:3:");
        $stmt->execute($cv_contact_id, $cv_id, $contacts_id);

    }
    public static function attach_user($cv_contact_id, $cv_id, $user_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_mtm_users WHERE cv_id=:1: AND user_id=:2: ");

        $stmt->execute($cv_id, $user_id);
        if($stmt->num_rows() > 0)
        {
            throw new Exception("CV Object $cv_id already has user $user_id attached.");
        }
        $stmt = $dbh->prepare("INSERT INTO cv_mtm_users 
                                           SET id=:1:, 
                                               cv_id=:2:, 
                                               user_id=:3:");
        $stmt->execute($cv_contact_id, $cv_id, $user_id);

    }
    public static function removeUser($cv_id, $user_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM cv_mtm_users WHERE cv_id=:1: AND user_id=:2: ");
        $stmt->execute($cv_id, $user_id);
    }
    
    public static function update_gl_account_receivable($cv_id, $account_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main
                                  SET gl_account_receivable=:2:
                                WHERE cv_id=:1:");
        $stmt->execute($cv_id, $account_id);

    }
    public static function update_gl_account_payable($cv_id, $account_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_main
                                  SET gl_account_payable=:2:
                                WHERE cv_id=:1:");
        $stmt->execute($cv_id, $account_id);

    }

    public static function get_cv_id_to_name_array()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_main ORDER BY cv_name  ");
        $stmt->execute();
        $set = $stmt->fetchall_assoc();
        $idarray = array();
        foreach($set as $row)
        {
                $idarray[$row['cv_id']]        = $row['cv_name'];
        }
        return $idarray;
        
    }
}

?>