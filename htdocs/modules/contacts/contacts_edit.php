<?
include("../../../framework/framework_masterinclude.php");
include("contacts_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "contacts_module"))
{
    echo "Permission Denied to access contacts module.";
    exit;
}

$ACTION =      '';
$careof =      '';
$street =      '';
$city =        '';
$state =       '';
$zip =         '';
$address_id = "NULL";
$contacts_id = '';
$phone_num =    '';
$phone_id = "NULL";
$email_id = "NULL";
$email_address = '';
$phonetype_id = "";

foreach ($_POST as $key => $value)
{
	$$key = $value;
	unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
	$$key = $value;
	unset($_GET->{$key});
}
switch($ACTION)
{
    case "Save Contact":
        if ($lastname != "")
        {
                contacts::add_contact($contacts_id, $lastname, $firstname, $ssn, $contacttype_id, $contact_notes);
        }
    break;
    case "Delete Contact":
        if ($contacts_id != "")
        {
                contacts::delete_contact($contacts_id);
                if($_SERVER['HTTPS'])
                {
                header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/contacts/contacts_main.php");
                }
                if(!$_SERVER['HTTPS'])
                {
                header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/contacts/contacts_main.php");
                }

        }
    break;
    case "Add Phone Number":
        contacts::add_contactphonenum($phone_id, $phone_num, $phonetype_id, $contacts_id);
        $phone_id = "NULL";
    break;
    case "Add Email Address":
        contacts::add_contactemailaddy($email_id, $email_address, $emailtype_id, $contacts_id);
        $email_id = "NULL";
    break;
    case "Add Address":
        contacts::add_contactaddress($address_id, $street, $city, $state, $zip, $addresstype_id, $contacts_id, $careof);
        $address_id = "NULL";
    break;
    case "Edit Address":
        $dbRow = contacts::get_address_from_id($contacts_address_edit); 
        $careof =      $dbRow['careof'];
        $street =      $dbRow['street'];
        $city =        $dbRow['city'];
        $state =       $dbRow['state'];
        $zip =         $dbRow['zip'];
        $address_id =  $dbRow['address_id'];
        $contacts_id = $dbRow['contacts_id'];
    break;
    case "Edit Email":
        $dbRow = contacts::get_email_from_email_id($contacts_email_edit); 
        $email_address =      $dbRow['email_address'];
        $emailtype_id =      $dbRow['emailtype_id'];
        $email_id =  $dbRow['email_id'];
        $contacts_id = $dbRow['contacts_id'];
    break;
    case "Edit Phone Number":
        $dbRow = contacts::get_phonenum_from_phone_id($contacts_phone_edit); 
        $phone_num =      $dbRow['phone_num'];
        $phonetype_id =      $dbRow['phonetype_id'];
        $phone_id =  $dbRow['phone_id'];
        $contacts_id = $dbRow['contacts_id'];
    break;
    case "Delete Email":
        if ($contacts_email_edit != "")
        {
	contacts::delete_email($contacts_email_edit);
        }
    break;
    case "Delete Address":
        if ($contacts_address_edit != "")
        {
                contacts::delete_address($contacts_address_edit);
        }
    break;
    case "Delete Phone Number":
        if ($contacts_phone_edit != "")
        {
                contacts::delete_phonenum($contacts_phone_edit);
        }
    break;

}

$contact_info = contacts::get_contact_from_id($contacts_id);
$types = contacts::getall_contacttypes();
$phones = contacts::getall_phonenumbers_from_contact_id($contacts_id);
$phonetypes = contacts::getall_contacts_phonetypes();
$emails = contacts::getall_emailaddys_from_contact_id($contacts_id);
$emailtypes = contacts::getall_contacts_emailtypes();
$addresses = contacts::getall_addresses_from_contact_id($contacts_id);
$addresstypes = contacts::getall_contacts_addresstypes();

include("contacts_edit.phtml");

?>
