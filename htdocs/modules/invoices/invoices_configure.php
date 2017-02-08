<?php
include("../../../framework/framework_masterinclude.php");
include("invoices_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$WARNING['show'] = false;

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
    case "Add Revenue Account":
        try{
        Invoice_System::create_revenue_account($account_id);
        }
        catch(MySQLException $exception){
            
        }
    break;
    case "Delete Revenue Account":
            Invoice_System::delete_revenue_account($invoice_account_edit);
    break;
    case "Set Default Revenue":
            Invoice_System::set_default_account($invoice_account_edit);
    break;
    case "Add Remittance Account":
        try{
        Invoice_System::create_remittance_account($account_id);
        }
        catch(MySQLException $exception){
            
        }
    break;
    case "Delete Remittance Account":
            Invoice_System::delete_remittance_account($invoice_account_edit);
    break;
    case "Set Default Remittance":
            Invoice_System::set_default_remittance_account($invoice_account_edit);
    break;

}
$row = transaction::get_accounttype_by_name('INCOME');
$income_set = transaction::getall_accounts_by_type($row['accounttype_id']);
$row = transaction::get_accounttype_by_name('LIABILITY');
$liability_set = transaction::getall_accounts_by_type($row['accounttype_id']);
$income_set = array_merge($income_set, $liability_set);
$row = transaction::get_accounttype_by_name('ASSET');
$asset_set = transaction::getall_accounts_by_type($row['accounttype_id']);

$revenue_accounts=Invoice_System::getall_revenue_accounts();
$remittance_accounts=Invoice_System::getall_remittance_accounts();

include("invoices_configure.phtml");


?>
