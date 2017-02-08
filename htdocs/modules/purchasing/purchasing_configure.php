<?
include("../../../framework/framework_masterinclude.php");
require_once("purchasing_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication
require_once("Purchasing_System.php");

$ACTION = '';
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$account_id = $_POST['account_id'];
    @$invoice_account_edit = $_POST['invoice_account_edit'];
}
$dbh = new DB_Mysql();

switch($ACTION)
{
    case "Add Expense Account":
        try{
        Purchasing_System::create_expense_account($account_id);
        }
        catch(MySQLException $exception){
            
        }
    break;
    case "Delete Expense Account":
            Purchasing_System::delete_expense_account($invoice_account_edit);
    break;
    case "Set Default Expense":
            Purchasing_System::set_default_account($invoice_account_edit);
    break;
    case "Add Remittance Account":
        try{
        Purchasing_System::create_disbursement_account($account_id);
        }
        catch(MySQLException $exception){
            
        }
    break;
    case "Delete Remittance Account":
            Purchasing_System::getall_disbursement_accounts($invoice_account_edit);
    break;
    case "Set Default Remittance":
            Purchasing_System::set_default_disbursement_account($invoice_account_edit);
    break;
        case "Add Clearing Account":
        try{
        Purchasing_System::create_clearing_account($account_id);
        }
        catch(MySQLException $exception){
            
        }
    break;
    case "Delete Clearing Account":
            Purchasing_System::getall_clearing_accounts($invoice_account_edit);
    break;
    case "Set Default Clearing":
            Purchasing_System::set_default_clearing_account($invoice_account_edit);
    break;
        case "Add AR/AP Clearing Account":
        try{
        Purchasing_System::create_arap_clearing_account($account_id);
        }
        catch(MySQLException $exception){
            
        }
    break;
    case "Delete AR/AP Clearing Account":
            Purchasing_System::getall_arap_clearing_accounts($invoice_account_edit);
    break;
    case "Set Default AR/AP Clearing":
            Purchasing_System::set_default_arap_clearing_account($invoice_account_edit);
    break;
    
}
$row = transactions::get_accounttype_by_name('EXPENSE');
$expense_set = transactions::getall_accounts_by_type($row['accounttype_id']);

$row = transactions::get_accounttype_by_name('ASSET');
$asset_set = transactions::getall_accounts_by_type($row['accounttype_id']);

$row = transactions::get_accounttype_by_name('LIABILITY');
$liability_set = transactions::getall_accounts_by_type($row['accounttype_id']);
$expense_set = array_merge($expense_set, $liability_set);
$asset_set = array_merge($asset_set, $liability_set);

$revenue_accounts=Purchasing_System::getall_expense_accounts();
$remittance_accounts=Purchasing_System::getall_disbursement_accounts();
$clearing_accounts=Purchasing_System::getall_clearing_accounts();
$arap_clearing_accounts=Purchasing_System::getall_arap_clearing_accounts();

include("purchasing_configure.phtml");


?>
