<?php
include("../../../framework/framework_masterinclude.php");
include("transactions_include.php");
Framework::authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$rowNumbers = 50;
$rowStart = 0;
$recurring_comment = '';
$recurring_amount = '';
$ORDERBY = 'type';
$VIEWALL = 'N';
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
switch($ACTION)
{
    case "Record":
        if ($recurring_id == "NULL")
        {
            $dc_line['dc'] = 'CREDIT';
            $dc_line['amount'] = $recurring_amount;
            $dc_line['account'] = $recurring_accountD;
            $dc_set[] = $dc_line;
            $dc_line['dc'] = 'DEBIT';
            $dc_line['amount'] = $recurring_amount;
            $dc_line['account'] = $recurring_accountW;
            $dc_set[] = $dc_line;
            recurring::add_recurring($recurring_id, $recurring_comment, $recurringtype_id, "", $dc_set);
        }
        break;

    case "Save": 
        if(isset($recurring_list))
        {
            foreach($recurring_list as $recurring_id)
            {
                unset($dc_set);
                $this_recurring = recurring::get_recurring_by_ID($recurring_id);
                //get all debits
                $this_debits = recurring::getall_debits_of_recurring($recurring_id);
                //get all credits
                $this_credits = recurring::getall_credits_of_recurring($recurring_id);
                //build array
                foreach($this_debits as $row)
                {
                    unset($dc_line);
                    $dc_line['dc'] = $row['recurring_dc'];
                    $dc_line['amount'] = $recurring[$recurring_id]['recurring_newamount'];
                    $dc_line['account'] = $row['recurring_account'];
                    $dc_set[] = $dc_line;
                }
                foreach($this_credits as $row)
                {
                    unset($dc_line);
                    $dc_line['dc'] = $row['recurring_dc'];
                    $dc_line['amount'] = $recurring[$recurring_id]['recurring_newamount'];
                    $dc_line['account'] = $row['recurring_account'];
                    $dc_set[] = $dc_line;
                }
                recurring::add_recurring($recurring_id, $recurring[$recurring_id]['recurring_comment'], $recurring[$recurring_id]['recurringtype_id'], "", $dc_set);
            }
        }
        break;
    case "Delete":        
        if(isset($recurring_list))
        {
            foreach($recurring_list as $recurring_main_ID)
            {
                recurring::delete_recurring($recurring_main_ID);
            }
        }
        break;
}
$dbh = new DB_Mysql();
$types = recurring::getall_recurring_types();
       
$rowNumbers = 25;
if(($rowStart == "") OR ($rowStart < 0))
{
    $rowStart = 0;	
}

if($VIEWALL == 'Y')
{
    $stmt = $dbh->prepare("SELECT *
        FROM recurring_main AS rm
        INNER JOIN recurring_type AS rt
                ON rt.recurringtype_id=rm.recurringtype_id
            ORDER BY recurringtype_name");
}
else
{
    $stmt = $dbh->prepare("SELECT *
        FROM recurring_main AS rm
        INNER JOIN recurring_type AS rt
                ON rt.recurringtype_id=rm.recurringtype_id
            ORDER BY recurringtype_name LIMIT $rowStart,$rowNumbers");

}
$stmt->execute();
$recurring = $stmt->fetchall_assoc();

$accountIDtoFullnameArray = transactions::build_accountIDtoFullname_array(false);
$account_array = transactions::build_account_stack_all();

include("transactions_recurring.phtml");


?>
