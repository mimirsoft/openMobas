<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../framework_masterinclude.php");
require_once("cv_master_include.php");
require_once("../../../classes/CV_Main.class.php");
require_once("../../../classes/Inventory_Type.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);



$ACTION = '';
$VALUE = '';
$VALUE2 = '';
foreach($_POST as $key => $value)
{
    $$key = $value;
}
foreach($_GET as $key => $value)
{
    $$key = $value;
}
switch($ACTION)
{
    case "target":
        $contactSet = contacts::getall_contacts_of_type($VALUE);
        $render = "<addrow>";
        $render .= "<removerow>contacts_id_row</removerow>";
        $render .= "<previousrow>target_row</previousrow>";
        $render .= "<label>Select Contact:</label>";
        $render .= "<rowname>contacts_id_row</rowname>";
        $render .= "<rowformname>contacts_id</rowformname>";
        foreach($contactSet as $row)
        {
            $render .= "<id>".$row['contacts_id']."</id><name>".framework::XML_Replace($row['lastname']);
            if($row['lastname'] == "")
            {
            $render .= "NO LAST NAME";    
            }
            if($row['firstname'] != "" )
            {
            $render .= ", ".framework::XML_Replace($row['firstname']);
            }
            $render .= "</name>\n";
        }
        $render .= "</addrow>";
        header('Content-Type: text/xml');
        echo $render;            
    break;
    case "item_type":
        switch($VALUE)
        {
            case "none":
                $render = "<addrow>";
                $render .= "<removerow>property_id_row</removerow>";
                $render .= "<previousrow>inventory_type</previousrow>";
                $render .= "<label>Property:</label>";
                $render .= "<rowname>property_id_row</rowname>";
                $render .= "<rowformname>native_table_id</rowformname>";
                $render .= "</addrow>";
                header('Content-Type: text/xml');
                echo $render;
            break;
            /*
            default:
                $type_info = Inventory_Type::get_inventory_type($VALUE);
                //if has a native table
                switch($type_info['nativetable_name'])
                    {

                        case "properties_main":
                            //get all items from native table
                            $properties = Property::getall_properties_by_status('CURRENT');
                            $render = "<addrow>";
                            $render .= "<removerow>property_id_row</removerow>";
                            $render .= "<previousrow>inventory_type</previousrow>";
                            $render .= "<label>Property:</label>";
                            $render .= "<rowname>property_id_row</rowname>";
                            $render .= "<rowformname>native_table_id</rowformname>";
                            foreach($properties as $row)
                            {
                                $render .= "<id>".$row['property_id']."</id><name>".framework::XML_Replace($row['property_address'])." ".$row['property_aptnum']."</name>\n";
                            }
                            $render .= "</addrow>";
                            header('Content-Type: text/xml');
                            echo $render;
                        break;
                    }
                
            break;
            */
        }
    break;

} 


?>
