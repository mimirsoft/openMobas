<?php
include("../../../framework/framework_masterinclude.php");
include("tenants_include.php");
require_once("../../../framework/classes/Maintenance.class.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION = '';
$lease_start_date_year = date('Y');
$lease_start_date_month =  date('m');
$lease_start_date_day = date('d');
$lease_end_date_year = date('Y');
$lease_end_date_month =  date('m');
$lease_end_date_day = date('d');
$tenant_id = '';
$tenant_current = '';
$firstname = '';
$lastname = '';
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $tenant_id = $_POST['tenant_id'];
    @$tenant_current = $_POST['tenant_current'];
    @$tenant_rent = $_POST['tenant_rent'];
    @$inventory_id = $_POST['inventory_id'];
    @$lease_start_date_year = $_POST['lease_start_date_year'];
    @$lease_start_date_month = $_POST['lease_start_date_month'];
    @$lease_start_date_day = $_POST['lease_start_date_day'];
    @$lease_end_date_year = $_POST['lease_end_date_year'];
    @$lease_end_date_month = $_POST['lease_end_date_month'];
    @$lease_end_date_day = $_POST['lease_end_date_day'];
    @$thirty_day_date_year = $_POST['thirty_day_date_year'];
    @$thirty_day_date_month = $_POST['thirty_day_date_month'];
    @$thirty_day_date_day = $_POST['thirty_day_date_day'];
}
$lease_start_date = $lease_start_date_year."-".$lease_start_date_month."-".$lease_start_date_day;
$lease_end_date = $lease_end_date_year."-".$lease_end_date_month."-".$lease_end_date_day;
$thirty_day_date = $thirty_day_date_year."-".$thirty_day_date_month."-".$thirty_day_date_day;

switch($ACTION)
{
    case "Update":
        Tenant::update($tenant_id, $tenant_current, $inventory_id, $lease_start_date, $lease_end_date);
        Tenant::updateRent($tenant_id, $tenant_rent);
    break;
    case "Delete Tenant":
        Tenant::delete($tenant_id);
    break;
    case "Set 30 Day":
        Tenant::update30Day($tenant_id, $thirty_day_date);
    break;
    case "Unset 30 Day":
        Tenant::update30Day($tenant_id, '');
    break;
    case "Create and Add Contact":
        if($lastname != "")
        {
            $contacts_id = contact::add_contact($contacts_id, $lastname, $firstname, $ssn, $contacttype_id, $contact_notes);
            $lastname = "";
            $firstname = "";
            $ssn = "";
            $contacttype_id = "";
            tenants::add_tenants_multi($tenantmulti_id, $tenant_id, $contacts_id);
            $contacts_id = '';
        }
        else
        {
            $warn = true;
            $warning = "Must Have Last Name Set";
        }
        break;
    case "Add Contact":
        tenants::add_tenants_multi($tenantmulti_id, $tenant_id, $contacts_id);
        break;


}
/*	This section retrieves property data for editing.
**
*/
$tenant_info = Tenant::getTenantByID($tenant_id);
$tenant_info['lease_start_date_year'] =     substr($tenant_info['lease_start_date'], 0 ,4);
$tenant_info['lease_start_date_month'] =    substr($tenant_info['lease_start_date'], 5 ,2);
$tenant_info['lease_start_date_day'] =      substr($tenant_info['lease_start_date'], 8 ,2);
$tenant_info['lease_end_date_year'] =     substr($tenant_info['lease_end_date'], 0 ,4);
$tenant_info['lease_end_date_month'] =    substr($tenant_info['lease_end_date'], 5 ,2);
$tenant_info['lease_end_date_day'] =      substr($tenant_info['lease_end_date'], 8 ,2);
$tenant_info['thirty_day_date_year'] =     substr($tenant_info['thirty_day_date'], 0 ,4);
$tenant_info['thirty_day_date_month'] =    substr($tenant_info['thirty_day_date'], 5 ,2);
$tenant_info['thirty_day_date_day'] =      substr($tenant_info['thirty_day_date'], 8 ,2);
$tenant_info['move_out_date_year'] =     substr($tenant_info['move_out_date'], 0 ,4);
$tenant_info['move_out_date_month'] =    substr($tenant_info['move_out_date'], 5 ,2);
$tenant_info['move_out_date_day'] =      substr($tenant_info['move_out_date'], 8 ,2);
$tenant_info['refund_date_year'] =     substr($tenant_info['refund_date'], 0 ,4);
$tenant_info['refund_date_month'] =    substr($tenant_info['refund_date'], 5 ,2);
$tenant_info['refund_date_day'] =      substr($tenant_info['refund_date'], 8 ,2);

$property_info = Property::get_property($tenant_info['property_id']);


$properties = Inventory_Item::getAllItemsWithExtended('property_id');

$type_ID = contact::get_contacttype_id_from_name('TENANT');
$contacts = contact::getall_contacts_of_type($type_ID);    

$tenant_contacts = CV_Main::getall_cv_contacts_from_id($tenant_info['cv_id']);

$contact_types = contact::getall_contacttypes();
$maintenance = maintenance::getAllOpenForInventoryID($tenant_info['inventory_id'], null);
include("tenants_edit.phtml");

?>
