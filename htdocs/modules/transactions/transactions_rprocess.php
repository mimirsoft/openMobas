<?
include("../../../framework/framework_masterinclude.php");
include("transactions_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';

$recurring_list = '';
$recurring = '';
$recurring_check = false;
$startdate_year = date('Y');
$startdate_month = date('m')-1;
if($startdate_month < 10)
{
    $startdate_month = "0".$startdate_month;
}
if($startdate_month < 1)
{
    $startdate_year--;
    $startdate_month = "12";
}
$startdate_day = "02";
$enddate_year = date('Y');
$enddate_month = date('m');
$enddate_day = "01";
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = "01";

if(isset($_POST['ACTION']))
{
    $recurring_check = @$_POST['recurring_check'];
    $recurring = @$_POST['recurring'];
    $recurring_type = @$_POST['recurring_type'];
    $recurring_list = @$_POST['recurring_list'];
    $startdate_day = @$_POST['startdate_day'];
    $startdate_month = @$_POST['startdate_month'];
    $startdate_year = @$_POST['startdate_year'];
    $enddate_year = @$_POST['enddate_year'];
    $enddate_month = @$_POST['enddate_month'];
    $enddate_day = @$_POST['enddate_day'];
    $ACTION = $_POST['ACTION'];
}

switch($ACTION)
{
    case "RECORD TRANSACTIONS":
        $recorddate_year = @$_POST['recorddate_year'];
        $recorddate_month = @$_POST['recorddate_month'];
        $recorddate_day = @$_POST['recorddate_day'];
        $date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;
        foreach($recurring_list as $recurring_id)
        {
            $this_recurring = recurring::get_recurring_by_ID($recurring_id);
            //get all debits
            $this_debits = recurring::getall_debits_of_recurring($recurring_id);
           //get all credits
            $this_credits = recurring::getall_credits_of_recurring($recurring_id);
            //build array
            unset($dc_set);
            foreach($this_debits as $row)
            {
                $dc_line['transaction_dc'] = $row['recurring_dc'];
                $dc_line['transaction_dc_amount'] = $row['recurring_dc_amount'];
                $dc_line['transaction_account'] = $row['recurring_account'];
                $dc_set[] = $dc_line;
            }
            foreach($this_credits as $row)
            {
                $dc_line['transaction_dc'] = $row['recurring_dc'];
                $dc_line['transaction_dc_amount'] = $row['recurring_dc_amount'];
                $dc_line['transaction_account'] = $row['recurring_account'];
                $dc_set[] = $dc_line;
            }
            //add transaction
            try{
                transactions::add_transaction("NULL", $date, $this_recurring['recurring_comment'], $recurring[$recurring_id]['checkno'], $dc_set, false);
                $line['recurring'] = $this_recurring;
                $line['dc_line'] = $dc_set;
                $processed[] = $line;
            }
            catch(TransactionException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] = $exception->message;
                echo  $WARNING['message'];
            }
        }
        $indexed_stack = transactions::build_account_stack_indexed(false);
        include("transactions_rprocessed.phtml");
        break;
    case "Get Recurring":
        $recurring=recurring::getall_recurring_of_type($recurring_type);
        $recurring_type_info = recurring::get_recurringtype_by_id($recurring_type);
        $types = recurring::getall_recurring_types();
        $accountIDtoNameArray = transactions::build_accountIDtoName_array();
        $indexed_stack = transactions::build_account_stack_indexed(false);
        if($recurring_check)
        {
            include("transactions_rprocess_check.phtml");
        }
        else{
            include("transactions_rprocess.phtml");
        }
        break;
    case "RECORD AND PRINT CHECKS":
        $recorddate_year = @$_POST['recorddate_year'];
        $recorddate_month = @$_POST['recorddate_month'];
        $recorddate_day = @$_POST['recorddate_day'];
        $date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;
        foreach($recurring_list as $recurring_id)
        {
            $this_recurring = recurring::get_recurring_by_ID($recurring_id);
            //get all debits
            $this_debits = recurring::getall_debits_of_recurring($recurring_id);
           //get all credits
            $this_credits = recurring::getall_credits_of_recurring($recurring_id);
            //build array
            unset($dc_set);

            foreach($this_debits as $row)
            {
                $dc_line['transaction_dc'] = $row['recurring_dc'];
                $dc_line['transaction_dc_amount'] = $recurring[$recurring_id]['recurring_checkamount'];
                $dc_line['transaction_account'] = $row['recurring_account'];
                $dc_set[] = $dc_line;
            }
            foreach($this_credits as $row)
            {
                $dc_line['transaction_dc'] = $row['recurring_dc'];
                $dc_line['transaction_dc_amount'] = $recurring[$recurring_id]['recurring_checkamount'];
                $dc_line['transaction_account'] = $row['recurring_account'];
                $dc_set[] = $dc_line;
            }
            //add transaction
            try{
                transactions::add_transaction("NULL", $date, $this_recurring['recurring_comment'], $recurring[$recurring_id]['recurring_checkno'], $dc_set, false);
                $line['recurring'] = $this_recurring;
                $line['dc_line'] = $dc_set;
                $processed[] = $line;
            }
            catch(TransactionException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] = $exception->message;
                echo  $WARNING['message'];
            }
        }
        $indexed_stack = transactions::build_account_stack_indexed(false);
        include("transactions_rprocessed.phtml");
        break;
    case "RECORD AND PRINT ELECTRONIC TRANSFER":
        $recorddate_year = @$_POST['recorddate_year'];
        $recorddate_month = @$_POST['recorddate_month'];
        $recorddate_day = @$_POST['recorddate_day'];
        $date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;
        foreach($recurring_list as $recurring_id)
        {
            $this_recurring = recurring::get_recurring_by_ID($recurring_id);
            //get all debits
            $this_debits = recurring::getall_debits_of_recurring($recurring_id);
           //get all credits
            $this_credits = recurring::getall_credits_of_recurring($recurring_id);
            //build array
            unset($dc_set);
            foreach($this_debits as $row)
            {
                $dc_line['transaction_dc'] = $row['recurring_dc'];
                $dc_line['transaction_dc_amount'] = $row['recurring_dc_amount'];
                $dc_line['transaction_account'] = $row['recurring_account'];
                $dc_set[] = $dc_line;
            }
            foreach($this_credits as $row)
            {
                $dc_line['transaction_dc'] = $row['recurring_dc'];
                $dc_line['transaction_dc_amount'] = $row['recurring_dc_amount'];
                $dc_line['transaction_account'] = $row['recurring_account'];
                $dc_set[] = $dc_line;
            }
            //add transaction
            try{
                transactions::add_transaction("NULL", $date, $this_recurring['recurring_comment'], $recurring[$recurring_id]['checkno'], $dc_set, false);
                $line['recurring'] = $this_recurring;
                $line['dc_line'] = $dc_set;
                $processed[] = $line;
            }
            catch(TransactionException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] = $exception->message;
                echo  $WARNING['message'];
            }
        }
        $indexed_stack = transactions::build_account_stack_indexed(false);
        include("transactions_rprocessed.phtml");
        break;
    default:
        $types = recurring::getall_recurring_types();
        $accountIDtoNameArray = transactions::build_accountIDtoName_array();
        $indexed_stack = transactions::build_account_stack_indexed(false);
        include("transactions_rprocess.phtml");
        break;

}


?>
