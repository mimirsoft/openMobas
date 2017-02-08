<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once("../../../framework/framework_masterinclude.php");
require_once("clients_include.php");
require_once("../../../framework/classes/CV_Main.class.php");
require_once("../../../framework/classes/Statement.class.php");
require_once("../../../framework/classes/Transaction.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "clients_module"))
{
    echo "Permission Denied to access clients module.";
    exit;
}

$system_id = 1;
$statement_object = Framework::get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);
$ACTION     =       '';     
$NOTLAST = '';
$statement_id = '';
$address_id = '';
$address = '';
$account_id = '';
$account_array = '';
$NOTLAST2 = false;
$addressed = 'NO';

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
	$cv_id = $_POST['cv_id'];
	$statement_id = $_POST['statement_id'];
	$account_array = $_POST['account_array'];
	$startdate_day = $_POST['startdate_day'];
	$startdate_month = $_POST['startdate_month'];
	$startdate_year = $_POST['startdate_year'];
	$enddate_day = $_POST['enddate_day'];
	$enddate_month = $_POST['enddate_month'];
	$enddate_year = $_POST['enddate_year'];
}

if($ACTION == "Make PDF")
{
    ob_start();
                    
}
if(is_array($account_array))
{
    $dates['start_day'] = $startdate_day;
    $dates['start_month'] = $startdate_month;
    $dates['start_year'] = $startdate_year;
    $dates['end_day'] = $enddate_day;
    $dates['end_month'] = $enddate_month;
    $dates['end_year'] = $enddate_year;
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM statements_main WHERE statement_id=:1:");
    $stmt->execute($statement_id);	
    $statement_object = $stmt->fetch_assoc();
    $statement_format = unserialize($statement_object['statement_array']);
    $accountIDtoNameArray = Transaction::build_accountIDtoName_array();
    $accountIDtoFullNameArray = Transaction::build_accountIDtoFullName_array(false);
    /*if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "all_statements"))
    {
        if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "public_statements"))
        {
            echo "Permission Denied for statement.";
            exit;
        }
        if(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "public_statements"))
        {
            if($statement_object['statement_privilege'] != 'PUBLIC')
            {
                echo "Statement not public.";
                exit;
 
            }
        }
    }
	*/
    if($addressed == "Y" && $address_id != "")
    {
        $address = contacts::get_address_from_id($address_id);
    }
    //Does the USER have permissions on this CV?
    $cv_set = CV_Main::getall_cv_for_user($USER->GetUserID());
    $CV_permission = false;
    foreach($cv_set as $cv)
    {
    	if($cv['cv_id'] == $cv_id)
    	{
    		$CV_permission = true;
    	}
    }
    if(!$CV_permission)
    {
    	echo "USER denied access to this Customer.";
    	exit;
    }
    $customer_info = CV_Main::get_cv_from_id($cv_id);
    	
    foreach($account_array as $account_id)
    {
        $can_access_account = false;
    	if($customer_info['gl_account_payable'] == $account_id || $customer_info['gl_account_receivable'] == $account_id )
        {
            $can_access_account = true;
        }       
        //Does the CV have permission on this account?
        if(!$can_access_account)
        {
            echo "Cannnot access account.";
            exit;
        }
        /* Here we build the actual statements.
        **
        */
        $NOTLAST = @next($account_array) or $NOTLAST = FALSE;
        if($account_id != "")
        {
            $statement = Statement::build_statement($statement_format, $account_id, $dates, $address, $accountIDtoNameArray, $accountIDtoFullNameArray, $BASE_DIR);
        }
        $statement['mailing_header'] = $addressed;
        $statement['date'] = $dates['end_year']."-".$dates['end_month']."-".$dates['end_day'];;
        include("../statements/statements_render.phtml");
    
    }
}
if($ACTION == "Make PDF")
{
    $data = ob_get_contents();
    ob_end_clean();
    include "statements_pdf.php";

}


?>

