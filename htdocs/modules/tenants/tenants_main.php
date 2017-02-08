<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("tenants_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

/* This adds tenants to the database.
** It takes data from the post, parses the post, creates variable with the key name,
** Then passes those variables to the add_owner function.
*/
$ACTION = '';
$tenants_main_rent = '';
$current_tenant = '';
$lease_start_date =  '';
$lease_end_date = '';
$lease_start_date_year = date('Y');
$lease_start_date_month =  date('m');
$lease_start_date_day = date('d');
$lease_end_date_year = date('Y')+1;
$lease_end_date_month =  date('m');
$lease_end_date_day = date('d');
$sortby = "";
$tenant_status = 'Y';
$property_status = 'CURRENT';
$create_new = false;
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$name = $_POST['name'];
    @$number = $_POST['number'];
    @$taxid = $_POST['taxid'];
    @$cv_default_address = $_POST['cv_default_address'];
    @$cv_default_city = $_POST['cv_default_city'];
    @$cv_default_state = $_POST['cv_default_state'];
    @$cv_default_zip = $_POST['cv_default_zip'];
    @$cv_default_email = $_POST['cv_default_email'];
    @$cv_default_phone = $_POST['cv_default_phone'];
    $create_new = $_POST['create_new'];
    $cv_id = $_POST['cv_id'];
    $tenant_rent = $_POST['tenant_rent'];
    $tenant_status = $_POST['tenant_status'];
    $inventory_id = $_POST['inventory_id'];
    $lease_start_date_year = $_POST['lease_start_date_year'];
    $lease_start_date_month = $_POST['lease_start_date_month'];
    $lease_start_date_day = $_POST['lease_start_date_day'];
    $lease_end_date_year = $_POST['lease_end_date_year'];
    $lease_end_date_month = $_POST['lease_end_date_month'];
    $lease_end_date_day = $_POST['lease_end_date_day'];
}
if(isset($_GET['sortby']))
{
    $sortby = $_GET['sortby'];
    $tenant_status = $_GET['tenant_status'];
    $property_status = $_GET['property_status'];
}
$lease_start_date = $lease_start_date_year."-".$lease_start_date_month."-".$lease_start_date_day;
$lease_end_date = $lease_end_date_year."-".$lease_end_date_month."-".$lease_end_date_day;

if ($ACTION == "Add Tenant")
{
    if($create_new)
    {
        $cv_id = CV_Main::create_cv_object("NULL", $name, $number, $taxid, 1, 0, $cv_default_address, $cv_default_city, $cv_default_state, $cv_default_zip, $cv_default_email, $cv_default_phone);
    }
    $tenant_id = Tenant::create($cv_id, $inventory_id, $lease_start_date, $lease_end_date);
    Tenant::updateRent($tenant_id, $tenant_rent);
    // set availability of inventory item to not available.  "0"
}
$type = Inventory_Type::getInventoryTypeByName('Monthly Rental');
$properties = Inventory_Item::getall_items_of_type($type['inventorytype_id']);

function compare_address($x, $y)
{
    if ( $x["item_name"] == $y["item_name"] )
    {
        return 0;
    }
    elseif ( $x["item_name"] < $y["item_name"] )
    return -1;
    else
    return 1;
} 
function lease_end($x, $y)
{
    if ( $x["lease_end_date"] == $y["lease_end_date"] )
    {
        return 0;
    }
    elseif ( $x["lease_end_date"] < $y["lease_end_date"] )
    return -1;
    else
    return 1;
} 
function lease_start($x, $y)
{
    if ( $x["lease_start_date"] == $y["lease_start_date"] )
    {
        return 0;
    }
    elseif ( $x["lease_start_date"] < $y["lease_start_date"] )
    return -1;
    else
    return 1;
} 
function street_cmp( $row1,$row2 )
{
    if ( ereg_replace ("[0-9]*[[:space:]]", "",$row1["item_name"]) == ereg_replace ("[0-9]*[[:space:]]", "", $row2["item_name"]))
    {
        return 0;
    }
    elseif ( ereg_replace ("[[0-9]*[[:space:]]", "", $row1["item_name"]) < ereg_replace ("[0-9]*[[:space:]]", "", $row2["item_name"]) )
    return -1;
    else
    return 1;
}

$tenants = Tenant::getAllTenantsByStatus($tenant_status, "thirty_day_date", "cv_name");

switch($sortby)
{
    case "lease_start":
        uasort($tenants,'lease_start') ;
    break;
    case "lease_end":
        uasort($tenants,'lease_end') ;
    break;
    case "street":
        usort($tenants, 'street_cmp');
    break;
    case "prop":
        uasort($tenants,'compare_address') ;
    break;
    default:
        //uasort($tenants,'lease_end') ;
    break;

}

$tenant_status_name['Y'] = "CURRENT";
$tenant_status_name['N'] = "OLD";
$box_title = $property_status." Properties - ".$tenant_status_name[$tenant_status]." Tenants";

$customers = CV_Main::getall_customers();

include("tenants_main.phtml");

?>
