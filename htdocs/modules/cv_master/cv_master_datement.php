<?
require_once("../../../framework/framework_masterinclude.php");
require_once("cv_master_include.php");
require_once("CV_Main.php");
Framework::authenticate("Limited");//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$name = '';
$number = '';
$taxid = '';
$is_customer = 0;
$is_vendor = 0;
$warn = false;
$customers = array();

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $name = $_POST['name'];
    $number = $_POST['number'];
    $taxid = $_POST['taxid'];
    $is_customer = $_POST['is_customer'];
    $is_vendor = $_POST['is_vendor'];
    @$cv_default_address = $_POST['cv_default_address'];
    @$cv_default_city = $_POST['cv_default_city'];
    @$cv_default_state = $_POST['cv_default_state'];
    @$cv_default_zip = $_POST['cv_default_zip'];
    @$cv_default_email = $_POST['cv_default_email'];
    @$cv_default_phone = $_POST['cv_default_phone'];
}


if ($ACTION == "Add Customer")
{

    if($name != "")
    {
        CV_Main::create_cv_object("NULL", $name, $number, $taxid, $is_customer, $is_vendor, $cv_default_address, $cv_default_city, $cv_default_state, $cv_default_zip, $cv_default_email, $cv_default_phone);
        $name = "";
        $number = "";
        $taxid = "";
        $is_customer = '';
        $is_vendor = "";
    }
    else
    {
        $warn = true;
        $warning = "Must Have Name Set";
    }
}

switch($ACTION)
{
    case "Update GL Accounts":
 
    break;
    case "Search Vendors":
    	if($name != "")
    	{
	    	$customers = CV_Main::search_vendors_by_name($name);
    	}
    	else
    	{
    		$customers = CV_Main::getall_vendors();
    	}
    break;
    case "Search Neither":
    	if($name != "")
    	{
	    	$customers = CV_Main::search_neither_by_name($name);
    	}
    	else
    	{
	    	$customers = CV_Main::getall_neither();
    	}
    break;
    case "Search Customers":
    	if($name != "")
    	{
	    	$customers = CV_Main::search_customers_by_name($name);
    	}
    	else
    	{
	    	$customers = CV_Main::getall_customers();
    	}
    break;
    case "Search All":
    	if($name != "")
    	{
	    	$customers = CV_Main::search_cv_by_name($name);
    	}
    	else
    	{
	    	$customers = CV_Main::getall_cv();
    	}
    break;
    default:
    break;
}
    	

//$customertypes = cv_manager::getall_customertypes();
//$users = Rbac_User::getAllAllowedTo("access_module", "cv_master_module");

//$users = $module->GetUsers();


include("cv_master_main.phtml");

?>
