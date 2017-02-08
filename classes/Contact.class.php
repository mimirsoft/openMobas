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
class ContactException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}
class Contact{
    
    public static function add_contact($ID, $last_name, $first_name, $ssn, $type, $notes)
    {
        if($last_name == "")
        {
            throw new Exception("Lastname is not set.");
        }
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO contacts_main 
                                           SET contacts_id=:1:, 
                                               lastname=:2:, 
                                               firstname=:3:, 
                                               ssn=:4:,
                                               contact_notes=:6:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE contacts_main 
                                      SET lastname=:2:, 
                                          firstname=:3:, 
                                          ssn=:4:, 
                                          contact_notes=:6: 
                                    WHERE contacts_id=:1:");
        }
        $stmt->execute($ID, $last_name, $first_name, $ssn, $type, $notes);
        return mysql_insert_id();

    }

    public static function add_activity($ID, $title, $comments, $user, $contacts_id)
    {
        $comments = nl2br($comments);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO contacts_activity 
                                       SET contact_activity_id=:1:, 
                                           contact_activity_title=:2:, 
                                           contact_activity_txt=:3:, 
                                           contact_activity_date=NOW(), 
                                           user_id=:4:, 
                                           contacts_id=:5: ");
        $stmt->execute($ID, $title, $comments, $user, $contacts_id);
    }
    function delete_contact($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM contacts_main 
                                     WHERE contacts_id=:1:");
        $stmt->execute($ID);
    }
    function delete_contacttype($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM contacts_type 
                                     WHERE contacttype_id=:1:");
        $stmt->execute($ID);
    }
    function delete_email($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM contacts_email 
                                     WHERE email_id=:1:");
        $stmt->execute($ID);
    }
    function delete_address($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM contacts_address 
                                     WHERE address_id=:1:");
        $stmt->execute($ID);
    }
    function delete_phonenum($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM contacts_phone 
                                     WHERE phone_id=:1:");
        $stmt->execute($ID);
    }

   public static function add_contactphonenum($ID, $num, $type, $main_ID)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO contacts_phone 
                                           SET phone_id=:1:,
                                               phone_num=:2:,
                                               phonetype_id=:3:,
                                               contacts_id=:4:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE contacts_phone 
                                           SET phone_num=:2:,
                                               phonetype_id=:3:,
                                               contacts_id=:4:
                                         WHERE phone_id=:1:");
        }
        $stmt->execute($ID, $num, $type, $main_ID);
    }
   public static function add_contactemailaddy($ID, $num, $type, $main_ID)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO contacts_email 
                                           SET email_id=:1:,
                                               email_address=:2:,
                                               emailtype_id=:3:,
                                               contacts_id=:4:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE contacts_email 
                                           SET email_address=:2:,
                                               emailtype_id=:3:,
                                               contacts_id=:4:
                                         WHERE email_id=:1:");
        }
        $stmt->execute($ID, $num, $type, $main_ID);
    }
    
    function add_contactaddress($ID, $street, $city, $state, $zip, $addresstype_ID, $contacts_ID, $careof)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO contacts_address 
                                           SET address_id=:1:, 
                                               street=:2:, 
                                               city=:3:, 
                                               state=:4:, 
                                               zip=:5:, 
                                               addresstype_id=:6:, 
                                               contacts_id=:7:, 
                                               careof=:8:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE contacts_address 
                                      SET street=:2:, 
                                          city=:3:, 
                                          state=:4:, 
                                          zip=:5:, 
                                          addresstype_id=:6:, 
                                          contacts_id=:7:, 
                                          careof=:8: 
                                    WHERE address_id=:1:");
        }
        $stmt->execute($ID, $street, $city, $state, $zip, $addresstype_ID, $contacts_ID, $careof);
    }	

    public static function get_contact_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *, CONCAT(lastname,', ',firstname) AS full_name FROM contacts_main WHERE contacts_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function getall_contacttypes()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM contacts_type ORDER BY contacttype_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_contacts_of_type($type)
    {
        $dbh = new DB_Mysql();
	$stmt = $dbh->prepare("SELECT *, CONCAT(lastname,', ',firstname) AS full_name FROM contacts_main
	                       INNER JOIN contact_tags 
                            ON contact_tags.contacts_id = contacts_main.contacts_id
                             WHERE contact_tags.contacttype_id=:1: ORDER BY lastname");
	$stmt->execute($type);
        $rows = $stmt->fetchall_assoc();
        $rows2 = array();
        foreach($rows as $row)
        {
            $row['full_name'] = framework::XML_Replace($row['full_name']);
            $rows2[] = $row;
        }
        return $rows2;
    }
    public static function get_contacttype_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM contacts_type WHERE contacttype_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function get_contacttype_id_from_name($type)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM contacts_type WHERE contacttype_name = :1:");
        $stmt->execute($type);
        $contacttype = $stmt->fetch_assoc();
        return $contacttype['contacttype_id'];
    }
    public static function add_contacttype($ID, $name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO contacts_type 
                                           SET contacttype_id=:1:,  
                                               contacttype_name=:2:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE contacts_type 
                                      SET contacttype_name=:2:
                                    WHERE contacttype_id=:1:");
        }
        $stmt->execute($ID, $name);
    }

    function add_phonetype($ID, $name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO contacts_phonetype 
                                           SET phonetype_id=:1:,  
                                               phonetype_name=:2:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE contacts_phonetype 
                                      SET phonetype_name=:2:
                                    WHERE phonetype_id=:1:");
        }
        $stmt->execute($ID, $name);
    }
    function add_emailtype($ID, $name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO contacts_emailtype 
                                           SET emailtype_id=:1:,  
                                               emailtype_name=:2:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE contacts_emailtype 
                                      SET emailtype_name=:2:
                                    WHERE emailtype_id=:1:");
        }
        $stmt->execute($ID, $name);
    }
    public static function getall_contacts_phonetypes()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM contacts_phonetype 
                            ORDER BY phonetype_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall_contacts_emailtypes()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM contacts_emailtype 
                            ORDER BY emailtype_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function get_contact_phonetype_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM contacts_phonetype WHERE phonetype_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function get_contact_emailtype_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM contacts_emailtype WHERE emailtype_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }

    function add_addresstype($ID, $name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO contacts_addresstype 
                                           SET addresstype_id=:1:,  
                                               addresstype_name=:2:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE contacts_addresstype 
                                      SET addresstype_name=:2:
                                    WHERE addresstype_id=:1:");
        }
        $stmt->execute($ID, $name);
    }
    public static function getall_contacts_addresstypes()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM contacts_addresstype 
                            ORDER BY addresstype_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function get_contacts_addresstypes_by_name($name)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM contacts_addresstype 
                                WHERE addresstype_name = :1:");
        $stmt->execute($name);
        return $stmt->fetch_assoc();
    }
    function getall_addresses_from_contact_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                         FROM contacts_addresstype AS AT, contacts_address AS A 
                        WHERE A.contacts_id = :1: AND AT.addresstype_id = A.addresstype_id");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    function getall_addresses_of_contacttype($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM contacts_main AS cm, contacts_address AS A 
                                WHERE cm.contacts_id = A.contacts_id 
                                  AND cm.contacttype_id = :1:
                             ORDER BY cm.lastname");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    function getall_phonenumbers_from_contact_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                         FROM contacts_phonetype AS PT, contacts_phone AS P 
                        WHERE P.contacts_id = :1: 
                          AND PT.phonetype_id = P.phonetype_id");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    function getall_emailaddys_from_contact_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                         FROM contacts_emailtype AS PT, contacts_email AS P 
                        WHERE P.contacts_id = :1:
                          AND PT.emailtype_id = P.emailtype_id");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    function get_email_from_email_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                         		 FROM contacts_emailtype AS PT, contacts_email AS P 
                           INNER JOIN contacts_main 
                  		           ON contacts_main.contacts_id = P.contacts_id 
                                WHERE P.email_id = :1: 
                                  AND PT.emailtype_id = P.emailtype_id");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    function get_phonenum_from_phone_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                         FROM contacts_phonetype AS PT, contacts_phone AS P 
                        WHERE P.phone_id = :1: 
                          AND PT.phonetype_id = P.phonetype_id");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function get_contact_addresstype_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM contacts_addresstype WHERE addresstype_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    function get_address_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM contacts_address 
                           INNER JOIN contacts_main 
                                   ON contacts_main.contacts_id = contacts_address.contacts_id 
                                WHERE address_id=:1:");
        $stmt->execute($ID);
        $contacts_address = $stmt->fetch_assoc();
        return $contacts_address;

    }

    function get_address_from_type_and_id($contacts_id, $addresstype)
    {
        $dbh = new DB_Mysql();
        // Use it to get the statement name
        $stmt = $dbh->prepare("SELECT * FROM contacts_main WHERE contacts_id='$contacts_id'");
        $stmt->execute();
        $contacts_main = $stmt->fetch_assoc();
        
        //Get Mailing Address Type ID
        $stmt = $dbh->prepare("SELECT addresstype_id FROM contacts_addresstype WHERE addresstype_name='$addresstype'");
        $stmt->execute();
        $dbRow = $stmt->fetch_assoc();
        $mailing_ID = $dbRow['addresstype_id'];
        
        //Set Appropriate Vars
        $stmt = $dbh->prepare("SELECT * FROM contacts_address WHERE contacts_id='$contacts_id' AND addresstype_id='$mailing_ID'");
        $stmt->execute();
        $contacts_address = $stmt->fetch_assoc();
        $contacts_address['firstname'] =  $contacts_main['firstname'];
        $contacts_address['lastname'] =   $contacts_main['lastname'];
        return $contacts_address;
        
    }
    public static function get_contacttag_to_name_array()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM contacts_type ORDER BY contacttype_name  ");
        $stmt->execute();
        $set = $stmt->fetchall_assoc();
        $idarray = array();
        foreach($set as $row)
        {
                $idarray[$row['contacttype_id']]        = $row['contacttype_name'];
        }
        return $idarray;
        
    }
    public static function addTag($cv_category_id, $cv_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO contact_tags 
                                       SET contacttype_id=:1:, 
                                           contacts_id=:2: ");
        try{
             $stmt->execute($cv_category_id, $cv_id);
        }
        catch(MysqlException $exception)
        {
            throw new ContactException("Contact ".$cv_id." already tagged with category ".$cv_category_id."<BR/>");
        }

    }   
    public static function deleteTag($leadcat_id, $leadID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM contact_tags 
                                       WHERE contacttype_id=:1:
                                       AND contacts_id=:2: ");

        $stmt->execute($leadcat_id, $leadID);
    }   
    function seach_contacts($search, $string, $cat)
    {
        $dbh = new DB_Mysql();
        // Use it to get the statement name
        switch($cat)
        {
            case "ALL":
                switch($search)
                {
                    case "firstname":
                        $stmt = $dbh->prepare("select *,contacts_main.contacts_id, GROUP_CONCAT(contacttype_id) AS contact_tag_string from contacts_main 
                                                LEFT JOIN contact_tags 
                                                    ON contact_tags.contacts_id = contacts_main.contacts_id
                                                    where firstname LIKE '%$string%'
                                                    GROUP BY contacts_main.contacts_id
                                                     ORDER BY lastname");
                    break;
                    case "lastname":
                        $stmt = $dbh->prepare("select *,contacts_main.contacts_id, GROUP_CONCAT(contacttype_id) AS contact_tag_string from contacts_main 
                                                    LEFT JOIN contact_tags 
                                                    ON contact_tags.contacts_id = contacts_main.contacts_id
                                                where lastname LIKE '%$string%' 
                                                GROUP BY contacts_main.contacts_id
                                                     ORDER BY lastname");
                    break;
                    default:
                        $stmt = $dbh->prepare("select *,contacts_main.contacts_id, GROUP_CONCAT(contacttype_id) AS contact_tag_string from contacts_main 
                                            LEFT JOIN contact_tags 
                                                ON contact_tags.contacts_id = contacts_main.contacts_id
                                                    where lastname LIKE '%$string%' 
                                                    GROUP BY contacts_main.contacts_id
                                                     ORDER BY lastname");
                    break;

                }
            break;
            default:
                switch($search)
                {
                    case "firstname":
                        $stmt = $dbh->prepare("select *,contacts_main.contacts_id, GROUP_CONCAT(contacttype_id) AS contact_tag_string from contacts_main 
                                                LEFT JOIN contact_tags 
                                                        ON contact_tags.contacts_id = contacts_main.contacts_id
                                                where firstname LIKE '%$string%' 
                                                AND contacttype_id = $cat
                                                GROUP BY contacts_main.contacts_id
                                                ORDER BY lastname");
                    break;
                    case "lastname":
                        $stmt = $dbh->prepare("select *,contacts_main.contacts_id, GROUP_CONCAT(contacttype_id) AS contact_tag_string from contacts_main 
                                                LEFT JOIN contact_tags 
                                                        ON contact_tags.contacts_id = contacts_main.contacts_id
                                                where lastname LIKE '%$string%' 
                                                AND contacttype_id = $cat
                                                GROUP BY contacts_main.contacts_id
                                                ORDER BY lastname");                    
                    break;
                    default:
                        $stmt = $dbh->prepare("select *,contacts_main.contacts_id, GROUP_CONCAT(contacttype_id) AS contact_tag_string from contacts_main 
                                                LEFT JOIN contact_tags 
                                                        ON contact_tags.contacts_id = contacts_main.contacts_id
                                                where lastname LIKE '%$string%' 
                                                AND contacttype_id = $cat
                                                GROUP BY contacts_main.contacts_id
                                                ORDER BY lastname");                    
                    break;
                }
        }
        $stmt->execute($string);
        $contacts_main = $stmt->fetchall_assoc();
        return $contacts_main;
    }
    function check_for_address($ID, $address)
    {
        $dbh = new DB_Mysql();
        // Use it to get the statement name
        $stmt = $dbh->prepare("select * from contacts_main
                                INNER JOIN contacts_address ON contacts_main.contacts_id = contacts_address.contacts_id 
                                WHERE contacts_main.contacts_id = :1:
                                AND contacts_address.street= :2:
                                LIMIT 1");
        $stmt->execute($ID, $address);
        $contacts_main = $stmt->fetch_assoc();
        return $contacts_main;
    }
    
}

?>
