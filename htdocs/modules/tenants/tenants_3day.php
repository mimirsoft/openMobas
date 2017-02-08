<?php

include("../../../framework/framework_masterinclude.php");
include("tenants_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$date_year = '';
$date_month = '';
$date_day = '';
$ACTION = '';
$empty = '';
$tenant_account = '';
foreach ($_POST as $key => $value)
{
    $$key = $value;
}


$dbh = new DB_Mysql();


$tenants = Tenant::getAllTenantsByStatus("Y", "thirty_day_date", "cv_name");
// we get every tenant for each property
foreach($tenants as $row)
{
    $tenant_contacts = CV_Main::getall_cv_contacts_from_id($row['cv_id']);  
    //$tenant_invoices = Invoice::getall_open_invoices_of_customer($row['cv_id']);
    $row['tenant_contacts'] = $tenant_contacts;
    $TenantsLines[] = $row;
    unset($row);
}



//transactions::build_accountIDtoName_array();
include("tenants_3day.phtml");


?>
