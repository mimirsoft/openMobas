<?

class calllog{

    public static function log_inquiry($inventory_id, $inquirer_firstname, $inquirer_lastname, $inquirer_phone, 
        $inquirer_phone2, $inquirer_email, $inquiry_type, $notes)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO inventory_inquiries
                                           SET inventory_id=:1:, 
                                               inquirer_firstname=:2:, 
                                               inquirer_lastname=:3:, 
                                               inquirer_phone=:4:, 
                                               inquirer_phone2=:5:, 
                                               inquirer_email=:6:, 
                                               inquiry_type=:7:,
                                               notes=:8:,
                                               inquiry_date=NOW()");
        $stmt->execute($inventory_id, $inquirer_firstname, $inquirer_lastname, $inquirer_phone, 
        $inquirer_phone2, $inquirer_email, $inquiry_type, $notes);
        return mysql_insert_id();
    }

}
?>
