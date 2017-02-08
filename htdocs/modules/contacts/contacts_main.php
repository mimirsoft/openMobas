<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once("../../../framework/framework_masterinclude.php");
require_once("contacts_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "contacts_module"))
{
    echo "Permission Denied to access contacts module.";
    exit;
}

$ACTION = '';
$firstname = '';
$lastname = '';
$ssn = '';
$results = Array();
$WARNING['show'] = false;

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $ssn = $_POST['ssn'];
    $contacttype_id = $_POST['contacttype_id'];
    $contact_notes = $_POST['contact_notes'];
    $phone_num = $_POST['phone_num'];
    $phonetype_id = $_POST['phonetype_id'];
    $contacts_id = $_POST['contacts_id'];
    $email_address = $_POST['email_address'];
    $emailtype_id = $_POST['emailtype_id'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $addresstype_id = $_POST['addresstype_id'];
    $careof = $_POST['careof'];
    $search_field = $_POST['search_field'];
    $search_string = $_POST['search_string'];
    $search_tag = $_POST['search_tag'];
    
    

}
switch($ACTION)
{
    case "Remove Tag":
        Contact::deleteTag($contacttype_id, $contacts_id);
    break;
    case "Remove Tags":
        foreach($contacts_id as $contact_element)
        {
            try{
                Contact::deleteTag($contacttype_id, $contact_element);
            }
            catch(CVException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH = $previous_search;
        
    break;
        case "Add Tag":
        foreach($contacts_id as $contact_element)
        {
            try{
                 Contact::addTag($contacttype_id, $contact_element);
            }
            catch(ContactException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH = $previous_search;
        
    break;
        case "Save Contact":
        if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "add_contact", "contacts_module"))
        {
            $WARNING['message'] = "Permission Denied to add contact in contacts module.";
            $WARNING['show'] = true;
        }
        if($lastname != "")
        {
            $contacts_id = contact::add_contact("NULL", $lastname, $firstname, $ssn, $contacttype_id, $contact_notes);
            
            $lastname = "";
            $firstname = "";
            $ssn = "";
        }
        else
        {
            $warn = true;
            $warning = "Must Have Last Name Set";
        }
        if($contacttype_id != "")
        {
            contact::addTag($contacts_id, $contacttype_id);
            $contacttype_id = "";
        }
        if($phone_num != "")
        {
            contact::add_contactphonenum("NULL", $phone_num, $phonetype_id, $contacts_id);
        }
        if($email_address != "")
        {
            contact::add_contactemailaddy("NULL", $email_address, $emailtype_id, $contacts_id);
        }
        if($street != "")
        {
            contact::add_contactaddress("NULL", $street, $city, $state, $zip, $addresstype_id, $contacts_id, $careof);
        }
        $contacts_id = "";
    break;
    case "Search":
        $results = contact::seach_contacts($search_field, $search_string, $search_tag);
    break;
}
$phonetypes = contact::getall_contacts_phonetypes();
$emailtypes = contact::getall_contacts_emailtypes();
$addresstypes = contact::getall_contacts_addresstypes();
$tag_id_to_name = contact::get_contacttag_to_name_array();
$types = contact::getall_contacttypes();
include("contacts_main.phtml");

?>
