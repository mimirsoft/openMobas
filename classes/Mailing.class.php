<?php

class mailing{

function email_stuff($from_name,$from_add,$to_add,$subject,$msg)
{
    $headers  = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\n";
    $headers .= "From: $from_name <$from_add>\n";
    $subject=stripslashes($subject);
    $msg=stripslashes($msg);
    mail("$to_add", "$subject", "$msg", "$headers") or die("Could not send e-mail...");
    return true;
}
function email_stuff_plaintxt($from_name,$from_add,$to_add,$subject,$msg)
{
    $headers  = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\n";
    $headers .= "From: $from_name <$from_add>\n";
    //$subject=stripslashes($subject);
    //$msg=stripslashes($msg);
    mail("$to_add", "$subject", "$msg", "$headers") or die("Could not send e-mail...");
    return true;
}
public static function get_mailing_from_id($ID)
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM mailing_stored WHERE mailing_id = :1:");
    $stmt->execute($ID);
    return $stmt->fetch_assoc();
}
public static function getall_mailings()
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM mailing_stored ORDER BY mailing_name  ");
    $stmt->execute();
    return $stmt->fetchall_assoc();
}

function add_mailing($ID, $name, $body)
{
    $dbh = new DB_Mysql();
    if($ID == "NULL")// If it is a new entry.
    {
        $stmt = $dbh->prepare("INSERT INTO mailing_stored 
                                        SET mailing_id=:1:,
                                            mailing_name=:2:,
                                            mailing_body=:3:");
    }
    else//If it is an edit to an existing entry
    {
        $stmt = $dbh->prepare("UPDATE mailing_stored 
                                  SET mailing_name=:2:,
                                      mailing_body=:3:
                                WHERE mailing_id=:1:");
    }
    $stmt->execute($ID, $name, $body);
}


}
?>