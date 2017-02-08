<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("available_include.php");

$ACTION =      '';
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
@$ACTION = $_POST['ACTION'];
@$inventory_id = $_POST['inventory_id'];
@$price = $_POST['price'];
    @$available = $_POST['available'];
    @$availabledate_date_year = $_POST['availabledate_date_year'];
    @$availabledate_date_month = $_POST['availabledate_date_month'];
    @$availabledate_date_day = $_POST['availabledate_date_day'];
}

switch($ACTION)
{
    case "UPDATE":
        require_once("../cv_master/Inventory_Item.class.php");
        if(isset($inventory_id))
        {
            foreach($inventory_id as $i_id)
            {
               try{
                    $dateavail = $availabledate_date_year[$i_id]."-".$availabledate_date_month[$i_id]."-".$availabledate_date_day[$i_id];
                    Inventory_Item::set_inventory_item_prices($i_id, $price[$i_id], $price[$i_id]);
                    Inventory_Item::update_availability($i_id, $available[$i_id], $dateavail);
                }
                catch(Exception $exception)
                {
                    $WARNING['show'] = true;
                    $WARNING['message'] = $exception->getMessage();
                }                
                
            }       
        
        }
    break;
}
        
$type = "default";
$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT * FROM properties_type ORDER BY propertytype_name");
$stmt->execute();
$propTypes = $stmt->fetchall_assoc();
if(isset($_GET['type']))
{
@$type = $_GET['type'];
}
if(isset($_POST['type']))
{
@$type = $_POST['type'];
}
@$sortby = $_GET['sortby'];
@$price_min = $_POST['price_min'];
@$price_max = $_POST['price_max'];
@$no_beds = $_POST['no_beds'];
@$no_baths = $_POST['no_baths'];
@$zip_code = $_POST['zip_code'];
$search_string = '';
$sql2 = "SELECT * 
        FROM inventory_items 
        INNER JOIN inventory_extended
        ON inventory_extended.inventory_id = inventory_items.inventory_id
        INNER JOIN properties_main
        ON inventory_extended.property_id = properties_main.property_id
        INNER JOIN user_main
        ON inventory_items.item_manager = user_main.user_id
        INNER JOIN cv_main
        ON cv_main.cv_id = inventory_items.cv_id
        WHERE property_status='CURRENT'
            AND available=1";
$stmt2 = $dbh->prepare($sql2);
$stmt2->execute($price_min, $price_max, $zip_code);
$results = $stmt2->fetchall_assoc();


foreach($results as $row)
{
    $stmt = $dbh->prepare("SELECT * 
                            FROM properties_files as pf, files_main as fm
                            WHERE pf.property_id=:1: 
                            AND pf.file_id = fm.file_id LIMIT 1");
    $stmt->execute($row['property_id']);
    $files[$row['property_id']] = $stmt->fetch_assoc();
    
}


include("available_main.phtml");

