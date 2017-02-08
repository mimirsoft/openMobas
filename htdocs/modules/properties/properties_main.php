<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("properties_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "properties_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}
$WARNING['show'] = false;

$dateavail_date_year = date('Y');
$dateavail_date_month =  date('m');
$dateavail_date_day = date('d');
$ACTION = '';

$address = '';
$aptnum = '';
$city = '';
$state = '';
$zip = '';
$county = '';
$thomasguidenum = '';
$propnum = '';
$propertytype_id = '';
$complex = '';
$area = '';
$garagenum = '';
$parknum = '';
$mailboxnum = '';
$sqft = '';
$yrblt = '';
$numrm = '';
$numbed = '';
$numbath = '';
$refridge = '';
$dishwasher = '';
$microwave = '';
$washer = '';
$dryer = '';
$stove = '';
$fireplace = '';
$heat = '';
$air = '';
$garage = '';
$garageinfo = '';
$openers = '';
$poolspa = '';
$landscaping = '';
$pets = '';
$petdeposit = '';
$smoking = '';
$whopayelec = '';
$whopaygarbage = '';
$whopaygas = '';
$whopaywater = '';
$rentmin = '';
$rentdesired = '';
$leasedesired = '';
$leasemin = '';
$feepercent  = '';
$property_avail = 'N';
$shortterm = '';
$hoa = '';
$hoa_number = '';
$hoa_manager = '';
$comments = '';
$description = '';
$petdescription = '';
$sortby = 'owner';
$property_status = '';
$prop['property_sale'] = 'N';
$prop['property_shortterm'] = 'N';
$prop['property_maintenance'] = 'N';
$parking = '';

foreach ($_POST as $key => $value)
{
	$$key = $value;
}
foreach ($_GET as $key => $value)
{
	$$key = $value;
}

$dateavail = $dateavail_date_year."-".$dateavail_date_month."-".$dateavail_date_day;
if ($ACTION == "SAVE" && $address != "")
{
           $property_id = Property::add_property($property_id, $address, $aptnum, $propnum, $city, $state, $county, $zip,  $thomasguidenum, 
                $complex, $area, $garagenum, $parknum, $mailboxnum, $sqft, $yrblt, $numrm, $numbed, $numbath, $refridge, $dishwasher, 
                $stove, $microwave, $fireplace, $air, $heat, $garage, $openers, $pool, $landscaping, 
                $pets, $petdeposit, $smoking, $whopaygas, $whopayelec, $whopaywater, $whopaygarbage, $rentdesired, $rentmin, 
                $leasedesired, $leasemin, $dateavail, $propertytype_id, $feepercent, $property_status, $property_avail, 
                $property_sale, $shortterm, $comments, $description, $petdescription, $garageinfo, $maintenance, $washer, $dryer, $parking);
                properties::updateHOA($property_id, $hoa);
                properties::updateHOANumber($property_id, $hoa_number);
                properties::updateHOAManager($property_id, $hoa_manager);
                properties::updateSpa($property_id, $spa);
                
}
$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT * FROM properties_type ORDER BY propertytype_name");
$stmt->execute();
$propTypes = $stmt->fetchall_assoc();


$current_props = Property::getall_properties_by_status('CURRENT');
//$current_props = Inventory_Item::getall_items_of_type('property_rental');
/*Here we retrieve the closed property listings from the database.
**
*/
$closed_props = Property::getAllPropertyNotStatus('CURRENT');
/*function namecmp( $row1,$row2 )
{
   return strcmp($row1['owners'][0]['fullname'], $row2['owners'][0]['fullname']) ;
}
function compare_address($x, $y)
{
    if ( $x["property_address"] == $y["property_address"] )
    {
        if ( $x["property_aptnum"] == $y["property_aptnum"] )
        return 0;
        elseif ( $x["property_aptnum"] < $y["property_aptnum"] )
        return -1;
        else
        return 1;
    }
    elseif ( $x["property_address"] < $y["property_address"] )
    return -1;
    else
    return 1;
} 
function compare_item($row1,$row2)
{
   return strcmp($row1['item_name'], $row2['item_name']) ;
} 
function compare_vendor( $row1,$row2 )
{
   return @strcmp($row1['cv_name'], $row2['cv_name']) ;
}
switch($sortby)
{
    case "prop":
        uasort($current_props,'compare_item') ;
        if(is_array($propertiesOld))
        {
        uasort($propertiesOld,'compare_address') ;
        }
        if(is_array($propertiesClosed))
        {
        uasort($propertiesClosed,'compare_address') ;
        }
    break;
    default:
        uasort($current_props,'compare_vendor') ;
        if(is_array($propertiesOld))
        {
        uasort($propertiesOld,'namecmp') ;
        }
        if(is_array($propertiesClosed))
        {
        uasort($propertiesClosed,'namecmp') ;
        }


}
*/
$property_id = "NULL";

include("properties_main.phtml");

