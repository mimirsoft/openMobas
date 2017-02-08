<?
include("../../../framework/framework_masterinclude.php");
include("transactions_include.php");
Framework::authenticate("Unlimited");//the two includes must be before the authentica, to supply the needed module name for authentication
if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "transactions_module")))
{
    echo "PERMISSION DENIED TO ACCESS MODULE TRANSACTIONS!!";
    exit;
}
$ACTION = '';
$results = array();

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $search_string = $_POST['search_string'];
    
}

switch($ACTION)
{
    case "Search Check Number":
        $results = transactions::getall_transactions_by_checkno($search_string);
    break;
    case "Search Amount":
        $results = transactions::getall_transactions_by_amount($search_string);
    break;
}

$account_array = transactions::build_accountIDtoFullname_array(false);
include("transactions_search.phtml");


?>






