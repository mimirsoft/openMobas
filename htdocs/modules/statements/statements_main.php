<?php
include("../../../framework/framework_masterinclude.php");
include("statements_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "statements_module"))
{
    echo "Permission Denied to access statement module.";
    exit;
}
$WARNING['show'] = false;

$startdate_year = date('Y', strtotime("-1 month"));
$startdate_month = date('m', strtotime("-1 month"));
$first_only = true;
$startdate_day = date('d');
$enddate_year = date('Y');
$enddate_month = date('m');
$enddate_day = date('d');
$VIEWALL = 'N';

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
if(isset($_GET['VIEWALL']))
{
    $VIEWALL = $_GET['VIEWALL'];
}

$prestartdate = $startdate_year."-".$startdate_month."-".($startdate_day-1);
$startdate = $startdate_year."-".$startdate_month."-".$startdate_day;
$enddate = $enddate_year."-".$enddate_month."-".$enddate_day;

if(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "all_statements"))
{
   $statement_list = statement::getall_statements();
}
elseif(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "public_statements"))
{
    $statement_list = statement::getall_public_statements();
}

if(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "all_accounts"))
{
    if($VIEWALL=="Y")
    {
         $account_array = transaction::build_accountIDtoFullname_array(false);
    }
    else
    {
        $account_array = transaction::build_accountIDtoFullname_array(true);
    }
    
}
elseif(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "own_accounts"))
{
    $user_mtm_account = transaction::getall_users_mtm_accounts_by_user_id($USER->GetUserID());
    foreach($user_mtm_account as $row)
    {
        $account_array[$row['account_id']] = transaction::retrieve_account_fullname($row['account_id']);
    } 

}
include("statements_main.phtml");


?>

