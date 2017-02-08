<?
/* Here we build the actual statements.
**
*/

// Retreive the escrow account ID

$customer_info = CV_Main::get_cv_from_id($cv_id);
$transactions_main_account = $customer_info['gl_account_payable'];
if ($ACTION == "MAKE STATEMENTS GL")
{
    $income_total = statements::retrieve_credits($customer_info['gl_account_payable'], $startdate, $splitdate);
    $result = Purchase_Order::sum_purchaseorder_of_vendor_date($customer_info['cv_id'], $splitdate, $enddate);
    $income_total += $result['SUM(po_total)'];
}    
elseif($ACTION == "MAKE STATEMENTS PO")
{
    $result = Purchase_Order::sum_purchaseorder_of_vendor_date($customer_info['cv_id'], $startdate, $enddate);
    $income_total = $result['SUM(po_total)'];
}

// Add to the total
$totalreport = $totalreport + $income_total;
$income = $income_total;

$firstname = "&nbsp;";
$lastname = $customer_info['cv_name'];
$ssn = $customer_info['tax_id'];
if ($ssn == "")
{
    $ssn = "&nbsp;";
}
$careof =   $customer_info['cv_default_careof'];
$address =  $customer_info['cv_default_address'];
$city =     $customer_info['cv_default_city'];
$state =    $customer_info['cv_default_state'];
$zip =     $customer_info['cv_default_zip'];

/* Here will print the form.
**
*/
include("yearend_1099.phtml");
 

?>
