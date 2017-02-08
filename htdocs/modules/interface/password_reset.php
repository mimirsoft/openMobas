
<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../../openMobas/framework_masterinclude.php");

$system_id = 1;
$statement_object = $FRAMEWORK->get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);

// Passkey that got from link
$passkey=$_GET['passkey'];

// Retrieve data from table where row that match this passkey
$row = $USER->get_user_from_confirm_code($passkey);
// If successfully queried
if($row){

    $password = $FRAMEWORK->createRandomPassword();
    // if successfully update the user table, email the new password to the user
    if($USER->set_password($row['user_id'], $password))
    {
        // ---------------- SEND MAIL FORM ----------------
        // send e-mail to ...
        $to=$row['user_email'];
        // Your subject
        $subject="Password Reset";
        // From
        $header="from: ".$SYS_INFO['COMPANY_GENERALEMAIL'];
        // Your message
        $message="Your password has been reset \r\n";
        $message.="your username is ".$row['username']."\r\n";
        $message.="your new password is $password\r\n";
         $message.="you may change your password in the user control section of the website";
        // send email
        $sentmail = mail($to,$subject,$message,$header);
    }
    else{
        echo "Password not reset";
    }
    // if your email succesfully sent
    if($sentmail){
        echo "Your username is $row[username].<BR/>";
        echo "An email containing your new password has been sent to your email address.<BR/>";
        echo "To return to the main page, click <a href=main.php>here.</a>";
    }
    else {
        echo "Password reset, cannot send Confirmation email to your e-mail address";
    }
}
// if not found passkey, display message "Wrong Confirmation code"
else {
    echo "Wrong Confirmation code";
}



?>
