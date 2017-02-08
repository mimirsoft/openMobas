<?php

/*This is the LeadZip functions file
**
*/
class LeadZipException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}
class LeadZip{

    public static function create($zip, $zipname)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO lead_zipcodes
                                       SET zip=:1:, 
                                           zip_name=:2:");
        $stmt->execute($zip, $zipname);
        
        return $stmt->dbh->insert_id;

    }
    public static function update($zip_id, $zip, $zipname)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE lead_zipcodes
                                       SET zip=:2:, 
                                           zip_name=:3:
                                           WHERE zip_id=:1:");
        $stmt->execute($zip_id, $zip, $zipname);
        return $stmt->dbh->insert_id;

    }
    public static function delete($zip_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM lead_zipcodes
                                          WHERE zip_id=:1:");
        $stmt->execute($zip_id);
        return $stmt->dbh->insert_id;

    }
    public static function createZipToUser($zip, $user)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO lead_zip_mtm_user
                                       SET zip_id=:1:, 
                                           user_id=:2:");
        $stmt->execute($zip, $user);
        return $stmt->dbh->insert_id;

    }
    public static function update_lead($ID, $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip,
                                       $comments, $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address,
                                        $user, $assignee, $whenfollowup, $desc, $origin=NULL, $prop_unit="")
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("     UPDATE lead_main 
                                       SET firstname=:2:, 
                                           lastname=:3:, 
                                           prop_street=:4:, 
                                           prop_city=:5:, 
                                           prop_state=:6:, 
                                           prop_zip=:7:, 
                                           comments=:8:, 
                                           street=:9:, 
                                           city=:10:, 
                                           state=:11:, 
                                           zip=:12:, 
                                           phone_num=:13:, 
                                           phone_num2=:14:, 
                                           email_address=:15:, 
                                           whenupdated_date=NOW(), 
                                           whoupdated_id=:16:, 
                                           whoassigned_id=:17:, 
                                           whenreturn_date=:18:,
                                           description=:19:,
                                           leadorigin_id=:20:,
                                           prop_unit=:21:
                                     WHERE lead_id=:1:");
        $comments = nl2br($comments);
        $desc = nl2br($desc);
        $stmt->execute($ID, $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip, $comments, 
                                        $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address, $user, $assignee, $whenfollowup, $desc, $origin, $prop_unit);
    }

    public static function getAssigned()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_zip_mtm_user
        						LEFT JOIN user_main 
        						ON  lead_zip_mtm_user.user_id = user_main.user_id 
        						LEFT JOIN lead_zipcodes 
        						ON  lead_zip_mtm_user.zip_id = lead_zipcodes.zip_id 
        						ORDER BY zip");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function getall()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_zipcodes ORDER BY zip");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    
    public static function get($zip)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_zipcodes WHERE zip_id=:1:");
        $stmt->execute($zip);
        return $stmt->fetch_assoc();
    }
    public static function deleteAssigned($zip, $user)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM lead_zip_mtm_user
                                       WHERE  zip_id=:1:
                                       AND user_id=:2:");
        $stmt->execute($zip, $user);
        return $stmt->dbh->insert_id;

    }
    
}
?>
