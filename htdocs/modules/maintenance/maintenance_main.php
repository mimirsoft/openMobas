<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//ini_set('error_reporting', E_ALL);

require_once("../../../framework/framework_masterinclude.php");
require_once("maintenance_include.php");
require_once("../../../framework/classes/Casesystem.class.php");
require_once("../../../framework/classes/Maintenance.class.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =     '';
$SORTBY = "whenopen_date";
$status = 'open';
$inventory_id = 'ALL';

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "maintenance_module"))
{
    echo "Permission access Maintenance Module";
    exit;
}

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
}



$users = Rbac_User::getAllAllowedTo("access_module", "maintenance_module");

Maintenance::checkFollowUp();

foreach($users as $user)
{
    $userArray[$user['user_id']] = $user['username'];
}
    $userArray[0] = "OPEN";
    
    if($status == 'open' && $inventory_id == 'ALL')
    {
        $cases = Maintenance::getAllOpen($SORTBY);
    }
    if($status == 'closed' && $inventory_id == 'ALL' )
    {
        $cases = Maintenance::getAllClosed($SORTBY);
    }
    if($status == 'both' && $inventory_id == 'ALL' )
    {
        $cases = Maintenance::getAll($SORTBY);
    }
    if($status == 'open' && $inventory_id != 'ALL' )
    {
        $cases = Maintenance::getAllOpenForInventoryID($inventory_id, $SORTBY);
    }
    if($status == 'closed' && $inventory_id != 'ALL' )
    {
        $cases = Maintenance::getAllClosedForInventoryID($inventory_id, $SORTBY);
    }
    if($status == 'both' && $inventory_id != 'ALL' )
    {
        $cases = Maintenance::getAllForInventoryID($inventory_id, $SORTBY);
    }
    
    $properties = Inventory_Item::getall_current_items_allowed_service();
    $prop_index = Property::get_id_to_property_hash();

    function street_cmp( $row1,$row2 )
    {
        if ( ereg_replace ("[0-9]*[[:space:]]", "",$row1["item_name"]) == ereg_replace ("[0-9]*[[:space:]]", "", $row2["item_name"]))
        {
            if ( @$row1["property_aptnum"] == @ $row2["property_aptnum"] )
            {
                return 0;
            }
            elseif ( ereg_replace ("[0-9]*[[:space:]]", "", $row1["item_name"]) < ereg_replace ("[0-9]*[[:space:]]", "",$row2["item_name"]))
            return -1;
            else
            return 1;
        }
        elseif ( ereg_replace ("[[0-9]*[[:space:]]", "", $row1["item_name"]) < ereg_replace ("[0-9]*[[:space:]]", "", $row2["item_name"]) )
        return -1;
        else
        return 1;
    }
    uasort($properties,'street_cmp');

include("maintenance_main.phtml");