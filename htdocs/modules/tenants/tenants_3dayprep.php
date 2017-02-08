<?php

include("../../../framework/framework_masterinclude.php");
include("tenants_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$system_id = 1;
$statement_object = Framework::get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);


$date_year = date('Y');
$date_month = date('m');
$date_day = date('d');
$ACTION = '';
$empty = '';
$tenant_account = '';
$names = '';
$owners = '';
$property_address = '';
$property_aptnum = '';
$property_city = '';
$property_county = '';
$property_state = '';
$property_zip = '';
foreach ($_POST as $key => $value)
{
    $$key = $value;
}

$tenant_info =  Tenant::getTenantByID($tenant_id);
$tenant_contacts = CV_Main::getall_cv_contacts_from_id($tenant_info['cv_id']);  
foreach($tenant_contacts as $row)
{
    $names .=  $row['lastname'].", ".$row['firstname']."<BR />";
}
$names .= "DOES 1-10 <BR />";



$inventory_info = Inventory_Item::get_inventory_item_from_id($tenant_info['inventory_id']);
echo $tenant_info['inventory_id'];
$owner_info = CV_Main::get_cv_from_id($inventory_info['cv_id']);
$owners =  $owner_info['cv_name']."<BR>C/O ".$SYS_INFO['COMPANY_NAME'];

$prop = Property::get_property($tenant_info['property_id']);

if($ACTION == "Print")
{
  $acount_info = transactions::get_account_byID($account_id);
      
}
else
{
 
    //Get all open invoices
    $transactions = Invoice::getall_open_invoices_of_customer($tenant_info['cv_id']);
    include("tenants_3dayprep.phtml");
}
?>
