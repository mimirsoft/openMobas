<?
include("../../../framework/framework_masterinclude.php");
include("transactions_include.php");
Framework::authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication

$date = '';
$date_year = '';
$date_month = '';
$date_day = '';
$ACTION = '';
$NULL = '';
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}

$date = $date_year."-".$date_month."-".$date_day;

$dbh = new DB_Mysql();
if ($ACTION == "Print" && isset($recurring_list))
{
    include("../checks/check.css");
    foreach($recurring_list as $recurring_main_ID)
    {
        $query = "SELECT * FROM recurring_main AS rm, recurring_check AS rc WHERE rm.recurring_main_ID=rc.recurring_main_ID AND rm.recurring_main_ID='$recurring_main_ID'";
        $dbResult = my_db_query($query);
        $dbRow = framework::my_db_fetcharray($dbResult);
        foreach ($dbRow as $key => $value)
        {
            $$key = $value;
        }
        $checkdate = 	$date_year."-".$date_month."-".$date_day;
        $checkname =$recurring_check_name;
        $careof = 	$recurring_check_careof;
        $address = 	$recurring_check_street;
        $city = 	$recurring_check_city;
        $state = 	$recurring_check_state;
        $zip = 		$recurring_check_zip;
        $memo = 	$recurring_check_memo;
        $net = 		${recurring_main_amount.$recurring_main_ID};
        if($net == 0)
        {
            $net = account_total($recurring_main_accountW);
        }
        $length = strlen($net);
        $thousands = floor($net/1000); // get the number of thousands
        $check_amount = "";
        if($thousands != 0)
        {
            $check_amount .= Framework::numeral_to_word($thousands); //convert it to a string
            $check_amount .= "THOUSAND "; //add the word THOUSAND to it
        }
        $hundreds = ($net%1000); // get the number of hundreds
        $hundred = floor($hundreds/100); // get the number of hundreds
        if($hundred != 0)
        {
            $check_amount .= Framework::numeral_to_word($hundred); //convert it to a string
            $check_amount .= "HUNDRED "; //add the word HUNDRED to it
        }
        $tens = ($hundreds%100); //get the number of tens
        $ten = floor($tens/10); //convert it to an integer.
        $ones = ($tens%10);
        $tens_as_word = Framework::tens_to_word($ten, $ones);
        if($ten != 1)
        {
            $tens_as_word .= Framework::numeral_to_word($ones);
        }
        $check_amount .= $tens_as_word;
        $cents = bcsub($net, floor($net), 2);
        $cents = $cents * 100;
        if($cents == '')
        {
            $cents = 0;
        }
        $check_amount = $check_amount . "AND " . $cents . "/100";

        include("../checks/checks_body.phtml");
        
    }

}
else{
    if ($ACTION == "Record" && isset($recurring_list))
    {
        foreach($recurring_list as $recurring_main_ID)
        {
            if(${recurring_main_amount.$recurring_main_ID} == 0)
            {
                ${recurring_main_amount.$recurring_main_ID} = account_total(${recurring_main_accountW.$recurring_main_ID});
            }
            add_transactions("NULL", $date, ${recurring_main_comment.$recurring_main_ID}, ${recurring_main_amount.$recurring_main_ID}, ${recurring_main_checkno.$recurring_main_ID});

        }
    }
    $stmt = $dbh->prepare("SELECT * FROM recurring_type ORDER BY recurringtype_name");
    $stmt->execute();
    $types = $stmt->fetchall_assoc();
    
    $stmt = $dbh->prepare("SELECT * FROM recurring_main");
    $stmt->execute();
    $recurring = $stmt->fetchall_assoc();
    
    include("transactions_rprocess.phtml");
}

?>
