<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once("../../../framework/framework_masterinclude.php");
require_once("checks_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "checks_module"))
{
    echo "Permission Denied to access check module.";
    exit;
}
$WARNING['show'] = false;
$checkdate_year =   date('Y');
$checkdate_month = date('m');
$checkdate_day =    date('d');
$CREATE     =       '';
$checkname =        '';
$checkname2 =       '';
$check_amount =     '';
$net =              '';
$memo   =           '';
$address    =       '';
$city       =       '';
$state      =       '';
$zip        =       '';
$careof     =       '';
$account_name =     '';
$enddate = '';
$startdate = '';
$warn =  false;

foreach ($_POST as $key => $value)
{
	$$key = $value;
	unset($_POST->{$key});
}

$checkdate = $checkdate_year."-".$checkdate_month."-".$checkdate_day;
$enddate = $checkdate_year."-".$checkdate_month."-".$checkdate_day;


/* Here we build the actual statements.
**
*/
$account_array = transaction::build_account_stack_all();

switch($CREATE)
{
    case "Print and Record Check":
        $comment = $checkname."-".$memo;
        $dc_line['transaction_dc'] = 'DEBIT';
        $dc_line['transaction_dc_amount'] = $net;
        $dc_line['transaction_account'] = $debit;
        $dc_set[] = $dc_line;
        $dc_line['transaction_dc'] = 'CREDIT';
        $dc_line['transaction_dc_amount'] = $net;
        $dc_line['transaction_account'] = $credit;
        $dc_set[] = $dc_line;
        //add transaction
        try{
            transaction::add_transaction("NULL", $checkdate, $comment, $checkno, $dc_set, false);
            $WARNING['show'] = true;
            $WARNING['message'] = "CHECK RECORDED: ".$comment." for ".$net;
        }
        catch(TransactionException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        }
        $check_amount = checks::check_amount($net);
        $net = number_format($net, 2);
        $checkname2 = $checkname;
        include("check.css");
        include("checks_body.phtml");
        break;
    default:
	   include("checks_main.phtml");
}


?>

