<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
//$_SERVER['SERVER_NAME'] = 'openmobas';
//require_once("../../../framework_masterinclude.php");

require_once("../../../../openMobas/framework_masterinclude.php");
require_once("leads_include.php");
//$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
//$WARNING['show'] = false;

//echo "WOOT\r\n";
//$whenreturn_date_year = date('Y');
//$whenreturn_date_month = date('m');
//$whenreturn_date_day = date('d');
//$whenreturn_date_hour = date("h");
//$whenreturn_date_minute = date("i");
//$NOW = $whenreturn_date_year."-".$whenreturn_date_month."-".$whenreturn_date_day." ".$whenreturn_date_hour.":".$whenreturn_date_minute;
$today = date("Y-m-d H:i:s");
$startDate = time();
$tomorrow = date('Y-m-d H:i:s', strtotime('+1 day', $startDate));

$leads2 = lead::getall_lead_with_followup_between($today, $tomorrow);
//print_r($leads2);
//get all leads with follow ups in next 24 hours

$userArray = $FRAMEWORK->getUserArray();

//email them 
foreach($leads2 as $row)
{
	//email lead assigned
	//tell them to email the person	
	// send e-mail to ...
	// $to='leads@pmsandiego.com';
	$to = $userArray[$row['whoassigned_id']]['user_email'];
	// Your subject
	$subject="NOTICE: FOLLOW UP FOR SCHEDULED FOR ".$tomorrow." FOR ".$row['firstname']." ".$row['lastname'];
	
	// From
	$header="from: LEADS@PMSANDIEGO.com <leads@pmsandiego.com>";
	// Your message
	$message="YOU HAVE A FOLLOW UP SCHEDULED FOR A LEAD \r\n";
	$message.= " \r\n";
	$message.="THIS IS AN AUTOMATED EMAIL REMINDER TO CONTACT THIS PERSON ON ".$tomorrow."\r\n";
	$message.= " \r\n";
	$message.= "NAME: ".$row['firstname']." ".$row['lastname']."\r\n";
	$message.= "PROPERTY ADDRESS: ".$row['prop_street']." ".$row['prop_unit']." \r\n";
	$message.= "PROPERTY CITY: ".$row['prop_city']." \r\n";
	$message.= "PROPERTY STATE: ".$row['prop_state']." \r\n";
	$message.= "PROPERTY ZIP: ".$row['prop_zip']." \r\n";
	$message.= "PHONE: ".$row['phone_num']." \r\n";
	$message.= "PHONE2: ".$row['phone_num2']." \r\n";
	$message.= "EMAIL: ".$row['email_address']." \r\n";
	$message.= "COMMENTS: ".$row['comments']." \r\n";
	$message.= " \r\n";
	$message.= "DESCRIPTION: ".$row['description']." \r\n";
	$message.= " \r\n";
	// send email
	$sentmail = mail($to,$subject,$message,$header);
	//$WARNING['show'] = true;
	//echo $to;
	//echo $message;
	
	
}	

?>