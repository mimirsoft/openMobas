<?
include("../../../framework/framework_masterinclude.php");
include("properties_include.php");
Framework::authenticate("Unlimited");//the two includes must be before the authentica, to supply the needed module name for authentication

$sortby = 'owner';

foreach ($_GET as $key => $value)
{
	$$key = $value;
}


$properties = properties::getall_properties_by_status('CURRENT');

function owner_cmp( $row1,$row2 )
{
    $comp =  strcmp($row1['cv_name'], $row2['cv_name']);
    if($comp > 0 )
    {
        return 1;
    }
    if($comp < 0 )
    {
        return -1; 
    }
    if($comp == 0 )
    {
        if ( $row1["property_address"] == $row2["property_address"] )
        {
            if ( $row1["property_aptnum"] == $row2["property_aptnum"] )
            {
                return 0;
            }
            elseif ( $row1["property_aptnum"] < $row2["property_aptnum"] )
            return -1;
            else
            return 1;
        }
        elseif ( $row1["property_address"] < $row2["property_address"] )
        return -1;
        else
        return 1;

    }

    
}
function addy_cmp( $row1,$row2 )
{
    if ( $row1["property_address"] == $row2["property_address"] )
    {
        if ( $row1["property_aptnum"] == $row2["property_aptnum"] )
        {
            return 0;
        }
        elseif ( $row1["property_aptnum"] < $row2["property_aptnum"] )
        return -1;
        else
        return 1;
    }
    elseif ( $row1["property_address"] < $row2["property_address"] )
    return -1;
    else
    return 1;
}
function zip_cmp( $row1,$row2 )
{
    if ( $row1["property_zip"] == $row2["property_zip"] )
    {
       return 0;
    }
    elseif ( $row1["property_zip"] < $row2["property_zip"] )
    return -1;
    else
    return 1;
}
function street_cmp( $row1,$row2 )
{
    if ( ereg_replace ("[0-9]*[[:space:]]", "",$row1["property_address"]) == ereg_replace ("[0-9]*[[:space:]]", "", $row2["property_address"]))
    {
        if ( $row1["property_aptnum"] == $row2["property_aptnum"] )
        {
            return 0;
        }
        elseif ( ereg_replace ("[0-9]*[[:space:]]", "", $row1["property_address"]) < ereg_replace ("[0-9]*[[:space:]]", "",$row2["property_address"]))
        return -1;
        else
        return 1;
    }
    elseif ( ereg_replace ("[[0-9]*[[:space:]]", "", $row1["property_address"]) < ereg_replace ("[0-9]*[[:space:]]", "", $row2["property_address"]) )
    return -1;
    else
    return 1;
}
switch($sortby)
{
    case "prop":
        usort($properties, 'addy_cmp');
    break;
    case "street":
        usort($properties, 'street_cmp');
    break;
    case "zip":
        usort($properties, 'zip_cmp');
    break;
    case "owner":
        usort($properties, 'owner_cmp');
    break;
    case "tenant":
        usort($properties, 'tenant_cmp');
    break;
    default:
        usort($properties, 'addy_cmp');
}

include("properties_print.phtml");

