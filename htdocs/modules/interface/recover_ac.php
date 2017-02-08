<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
$system_id = 1;
$statement_object = $FRAMEWORK->get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);

// values sent from form
$email=$_POST['email'];

//get user from email

// Insert data that retrieves from "temp_members_db" into table "registered_members"
$result = $USER->get_user_from_email($email);
if(!$result)
{
  header('Location: recover.php?message=notfound');
  exit;
}
// Random confirmation code
$confirm_code=md5(uniqid(rand()));

// Insert data into database
// if suceesfully inserted data into database, send confirmation link to email
if($USER->update_confirm_code($result['user_id'], $confirm_code)) 
{
    // ---------------- SEND MAIL FORM ----------------
    // send e-mail to ...
    $to=$result['user_email'];
    // Your subject
    $subject="Username/Password Recovery";
    // From
    $header="from: ".$SYS_INFO['COMPANY_GENERALEMAIL'];
    // Your message
    $message="Click on this link to reset your password \r\n";
    $message.="Your new password will be emailed to you shortly \r\n";
    $message.="https://".$SYS_INFO['COMPANY_BASE_URL'].$BASE_DIR."/interface/password_reset.php?passkey=$confirm_code";
    // send email
    $sentmail = mail($to,$subject,$message,$header);

}
// if not insert
else {
echo "Unable to recover your account.";
}

// if your email succesfully sent
if($sentmail){
echo "Your username is $result[username].<BR/>";
echo "An Email to recover your username/password has been sent to your email address.<BR/>";
echo "To return to the home page, click <a href=main.php>here.</a>";
echo $header;

}
else {
echo "Cannot send Confirmation link to your e-mail address";
}

?>