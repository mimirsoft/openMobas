<?
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../../../framework/framework_masterinclude.php");
include("statements_include.php");
framework::authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);//the two includes must be before the authentica, to supply the needed module name for authentication


if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "statements_module"))
{
    echo "Permission Denied to access statement module.";
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

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
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
    if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "all_statements"))
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

    if($addressed == "Y" && $address_id != "")
    {
        $address = contacts::get_address_from_id($address_id);
    }


    foreach($account_array as $account_id)
    {
        if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "all_accounts"))
        {
             if(!Transaction::getall_users_mtm_accounts_by_user_id_and_account_id($USER->GetUserID(), $account_id))
             {
                echo "Cannnot access account.";
                exit;

             }

        }
        
        if($USER->CheckPermissionType($MODULE_NAME) == 'L')
        {
            if($account_id == "")
            {
                echo "NO ACCOUNT SELECTED";
                exit;
            } 
            $limited_permissions = $USER->GetLimitedPermissions($MODULE_NAME);
            if(@$limited_permissions['transactions']['transactions_accounts'][$account_id] != 1)
            {
                echo "NO PERMISSION FOR THIS ACCOUNT";
                exit;
            } 
        
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
        include("statements_render.phtml");

    }
}

if($ACTION == "Make PDF")
{
    $data = ob_get_contents();
    ob_end_clean();
    include "statements_pdf.php";

}


?>

