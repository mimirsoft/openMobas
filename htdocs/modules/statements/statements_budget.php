<?
include("../../../framework/framework_masterinclude.php");
include("statements_include.php");
include("budget_functions.php");
framework::authenticate("Unlimited");//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$enddate_year = date('Y');
$enddate_month = date('m');
$enddate_day = date('d');
$startdate_year = date('Y');
$startdate_month = date('m');
$startdate_day = date('d');
$budgetline_amount = 0.00;
$budgetline_ID = 'NULL';
$budgetline_monthly = '';
$budgetline_yearly = '';
$budgetline_account = '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
$startdate = $startdate_year."-".$startdate_month."-".$startdate_day;
$enddate = $enddate_year."-".$enddate_month."-".$enddate_day;

switch($ACTION)
{
    case "EDIT":
        $stmt = $dbh->prepare("SELECT * FROM statements_main WHERE statement_id=:1:");
        $stmt->execute($edit_statement);	
        $statement_object = $stmt->fetch_assoc();
        $statement = unserialize($statement_object['statement_array']);
        $statement_id = $statement_object['statement_id'];
        break;
    case "Add Budget Item":
        if($budgetline_account != "")
        {
            statements::save_budget($budgetline_ID, $startdate, $enddate, $budgetline_account, $budgetline_monthly, $budgetline_yearly);
        }
        break;
    case "New Budget":
        $account_type = transactions::get_accounttype_by_name("INCOME");
        $income_accounts = transactions::getall_accounts_by_type($account_type['accounttype_id']);
        $account_type = transactions::get_accounttype_by_name("EXPENSE");
        $expense_accounts = transactions::getall_accounts_by_type($account_type['accounttype_id']);
        foreach($income_accounts as $row)
        {
            $income_parents[$row['account_id']] = 1;
        }        
        break;
}

$select_array = transactions::build_accountIDtoFullName_array(true);
$budget_lines = budget::getall_budgetlines();

$account_array = transactions::build_account_stack_all();


include("statements_budget.phtml");


?>

