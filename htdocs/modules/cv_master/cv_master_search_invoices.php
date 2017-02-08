<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("cv_master_include.php");
require_once("CV_Main.class.php");
require_once("../invoices/Invoice.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$startdate_year = date('Y');
$startdate_month = date('m');
$startdate_day = "01";
$enddate_year = date('Y');
$enddate_month = date('m');
$enddate_day = "31";
$invoices = '';
$purchases = '';
$purchaseGroupInvoice = '';

if(isset($_GET['ACTION']))
{
    $ACTION = $_GET['ACTION'];
    $enddate = $_GET['enddate'];
    $startdate = $_GET['startdate'];
    $search_item = $_GET['search_item'];
    $startdate_year = date('Y', strtotime($startdate));
    $startdate_month = date('m', strtotime($startdate));
    $startdate_day = date('d', strtotime($startdate));
    $enddate_year = date('Y', strtotime($enddate));
    $enddate_month = date('m', strtotime($enddate));
    $enddate_day = date('d', strtotime($enddate));
    
}
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $startdate_year = $_POST['startdate_year'];
    $startdate_month = $_POST['startdate_month'];
    $startdate_day = $_POST['startdate_day'];
    $enddate_year = $_POST['enddate_year'];
    $enddate_month = $_POST['enddate_month'];
    $enddate_day = $_POST['enddate_day'];
    $search_item = $_POST['search_item'];
    $startdate = $startdate_year."-".$startdate_month."-".$startdate_day;
    $enddate = $enddate_year."-".$enddate_month."-".$enddate_day;
    if(isset($_POST['startdate']))
    {
      $enddate = $_POST['enddate'];
      $startdate = $_POST['startdate'];
      
    }
}


switch($ACTION)
{
    case "Get Data":
        //$results = Inventory_Item::get_sales_and_purchase_total_dates($startdate, $enddate);
        $invoices = Invoice::getAllInvoicesWithItemBetweenDates($search_item, $startdate, $enddate);
        $purchases = Purchase_Order::getAllPurchasesWithItemBetweenDates($search_item, $startdate, $enddate);
        //$purchaseToInvoice = Purchase_Order::getAllPurchaseReqMatchInvoiceOfItemBetweenDates($search_item, $startdate, $enddate);
        $purchaseGroupInvoice = Purchase_Order::getAllPurchasesGroupByInvoiceBetweenDates($search_item, $startdate, $enddate);
        break;
    case "Print Data":
        //$results = Inventory_Item::get_sales_and_purchase_total_dates($startdate, $enddate);
        $invoices = Invoice::getAllInvoicesWithItemBetweenDates($search_item, $startdate, $enddate);
        include("cv_master_search_invoices_print.phtml");
        exit;
    break;
    case "Monthly Interval":
        
function lastday($month = '', $year = '') {
    if (empty($month)) {
      $month = date('m');
    }
    if (empty($year)) {
      $year = date('Y');
    }
    $result = strtotime("{$year}-{$month}-01");
    $result = strtotime('-1 second', strtotime('+1 month', $result));
    return date('Y-m-d', $result);
}
$start = $month = strtotime('2009-01-01');
$end = strtotime('2012-01-01');
$month_set = array();
while($month < $end)
{
    $monthly_count = Inventory_Item::getSalesAndPurchaseTotalDatesForItem($search_item, date('Y-m-d', $month), lastday(date('m', $month), date('Y', $month)));
    //$month_set = array_merge($month_set,$monthly_count);
    $monthly_count['date'] = date('Y-m-d', $month);
    $month_set[] = $monthly_count;
    //date('F Y', $month);
    $month = strtotime("+1 month", $month);
}

/*
$startdate_year;
date("Ymd");

        $month_set
        for($i=1; $i<12, $i++)
        {        
            $monthly_count = Inventory_Item::getSalesAndPurchaseTotalDatesForItem($search_item, $startdate, lastday($i, $startdate_year));
            $month_set[] = $monthly_count;
        }
  */
    
    break;
    default:
    break;
}
    	
$items = Inventory_Item::getall_current_items();


include("cv_master_search_invoices.phtml");

?>
