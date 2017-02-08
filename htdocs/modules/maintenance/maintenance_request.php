<?php
include("../../../framework/framework_masterinclude.php");
require_once("maintenance_include.php");
Framework::XML_authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication


$ACTION = '';
$VALUE = '';
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
	case "inventory_id":
		$customers = Invoice::getall_customers_purchasing_item($VALUE);
        $item_info  =  Inventory_Item::get_inventory_item_from_id($VALUE);
            
		$addrow['previousrow'] = 'inventory_id_row';
        $addrow['label'] = 'Notes:';
        $addrow['meta'][] = $item_info['item_notes'];
        $addrow['removerow'] = 'notes_row';
        $addrow['rowname'] = 'notes_row';
        $addrow['rowformname'] = 'notes';
        $result[] = $addrow;
        unset($addrow);        
        $addrow['previousrow'] = 'notes_row';
        $addrow['label'] = 'Customer:';
        $addrow['removerow'] = 'customer_row';
        $addrow['rowname'] = 'customer_row';
        $addrow['rowformname'] = 'customer';
        $addrow['id'][] = 'none';
        $addrow['name'][] = '';
        foreach($customers as $row)
        {
            $addrow['id'][] = $row['cv_id'];
            $addrow['name'][] = $row['cv_name'];
        }
        $result[] = $addrow;
        
        
        break;
	case "customer":
		$customer_info = CV_Main::get_cv_from_id($VALUE);
	    $contacts = CV_Main::getall_cv_contacts_from_id($VALUE);
		$addrow['previousrow'] = 'customer_row';
        $addrow['label'] = 'Customer Info:';
        $addrow['removerow'] = 'cv_info';
        $addrow['rowname'] = 'cv_info';
        $addrow['rowformname'] = 'cv_info';
        $addrow['meta'][] = framework::XML_Replace($customer_info['cv_name']).", ".framework::XML_Replace($customer_info['cv_default_phone']);
		if(is_array($contacts))
        {
            foreach($contacts as $contact)
            {
                $contact_info = contact::get_contact_from_id($contact['contacts_id']);
                $addrow['meta'][] = framework::XML_Replace($contact['firstname'])." ".framework::XML_Replace($contact['lastname']);
		        $phones = contact::getall_phonenumbers_from_contact_id($contact['contacts_id']);
		        if(is_array($phones))
                {
                    foreach($phones as $phone)
                    {
                        $addrow['meta'][] = framework::XML_Replace($phone['phone_num']);
                    } 
                }
            }
        }
        $result[] = $addrow;
        unset($addrow);        
        
		break;
	case "contacttype":
		$contacts = contact::getall_contacts_of_type($VALUE);
        $addrow['previousrow'] = 'contacttype_row';
        $addrow['label'] = 'Contact:';
        $addrow['removerow'] = 'contacts_row';
        $addrow['rowname'] = 'contacts_row';
        $addrow['rowformname'] = 'contacts';
        $addrow['id'][] = 'NULL';
        $addrow['name'][] = '';
        foreach($contacts as $row)
        {
            $addrow['id'][] = $row['contacts_id'];
            $addrow['name'][] = framework::XML_Replace($row['lastname']).", ".framework::XML_Replace($row['firstname']);
        }
        $result[] = $addrow;
		break;
	case "contacts":
		if($VALUE == "NULL")
		{
            $addrow['previousrow'] = 'contacttype_row';
            $addrow['removerow'] = 'contact_address_row';
            $result[] = $addrow;
			break;
		}
		$addys = contact::getall_addresses_from_contact_id($VALUE);
        $addrow['previousrow'] = 'contacts_row';
        $addrow['label'] = 'Address:';
        $addrow['removerow'] = 'contact_address_row';
        $addrow['rowname'] = 'contact_address_row';
        $addrow['rowformname'] = 'address_id';
        foreach($addys as $row)
        {
            $addrow['id'][] = $row['contacts_id'];
            $addrow['name'][] = framework::XML_Replace($row['lastname']).", ".framework::XML_Replace($row['firstname']);
        }
        $result[] = $addrow;
    break;
}

echo(json_encode($result));

?>
