<?php
include("../../../framework/framework_masterinclude.php");
include("tenants_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$type_id = '';
$sortby = '';
foreach ($_GET as $key => $value)
{
	$$key = $value;
	unset($_POST->{$key});
}

if($tenant_status == 'CURRENT')
{
    $tenant_status_yn = 'Y';
}
else
{
    $tenant_status_yn = 'N';
}

$lines = Tenant::getAllTenantsByStatus($tenant_status, "thirty_day_date", "cv_name");

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
        //uasort($tenants,'compare_address') ;
    break;

}

include("tenants_print.phtml");

?>
