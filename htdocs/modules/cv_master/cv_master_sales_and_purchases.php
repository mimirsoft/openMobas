<?
require_once("../../../framework/framework_masterinclude.php");
require_once("cv_master_include.php");
require_once("CV_Main.class.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$startdate_year = date('Y');
$startdate_month = date('m');
$startdate_day = "01";
$enddate_year = date('Y');
$enddate_month = date('m');
$enddate_day = "31";
$results = '';
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $startdate_year = $_POST['startdate_year'];
    $startdate_month = $_POST['startdate_month'];
    $startdate_day = $_POST['startdate_day'];
    $enddate_year = $_POST['enddate_year'];
    $enddate_month = $_POST['enddate_month'];
    $enddate_day = $_POST['enddate_day'];
}

$startdate = $startdate_year."-".$startdate_month."-".$startdate_day;
$enddate = $enddate_year."-".$enddate_month."-".$enddate_day;

switch($ACTION)
{
    case "Get Data":
            $results = Inventory_Item::get_sales_and_purchase_total_dates($startdate, $enddate);
    break;

    default:
    break;
}
    	


include("cv_master_sales_and_purchases.phtml");

?>
