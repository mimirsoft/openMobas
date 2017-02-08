<?
include("../../../framework/framework_masterinclude.php");
include("contacts_include.php");
Framework::authenticate("Unlimited");//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$phonetype_id = ''; 
$phonetype_name = '';
$emailtype_id = ''; 
$emailtype_name = '';
$contacttype_id = '';
$contacttype_name = '';
$addresstype_id = '';
$addresstype_name = '';
foreach ($_POST as $key => $value)
{
    $$key = $value;
}

if ($ACTION == "Save Contact Type")
{
    if($contacttype_name != "")
    {
    contacts::add_contacttype($contacttype_id, $contacttype_name);
    $contacttype_id = "NULL";
    }
}

if ($ACTION == "Edit Contact Type")
{
    $dbRow = contacts::get_contacttype_from_id($contacts_type_edit);
    $contacttype_name =    $dbRow['contacttype_name'];
    $contacttype_id =      $dbRow['contacttype_id'];
}
if ($ACTION == "Delete Contact Type")
{
    $dbRow = contacts::delete_contacttype($contacts_type_edit);
}

if ($ACTION == "Save Contact Phone Type")
{
    contacts::add_phonetype($phonetype_id, $phonetype_name);
    $phonetype_id = "NULL";
}


if ($ACTION == "Edit Contact Phone Type")
{
    $dbRow = contacts::get_contact_phonetype_from_id($phonetype_edit);
    $phonetype_name = $dbRow['phonetype_name'];
    $phonetype_id = $dbRow['phonetype_id'];
}

if ($ACTION == "Save Contact Email Type")
{
    contacts::add_emailtype($emailtype_id, $emailtype_name);
    $emailtype_id = "NULL";
}
if ($ACTION == "Edit Contact Email Type")
{
    $dbRow = contacts::get_contact_emailtype_from_id($emailtype_edit);
    $emailtype_name = $dbRow['emailtype_name'];
    $emailtype_id = $dbRow['emailtype_id'];
}

if ($ACTION == "Save Contact Address Type")
{
    contacts::add_addresstype($addresstype_id, $addresstype_name);
    $addresstype_id = "NULL";
}

if ($ACTION == "Edit Contact Address Type")
{
    $dbRow = contacts::get_contact_addresstype_from_id($addresstype_edit);
    $addresstype_name = $dbRow['addresstype_name'];
    $addresstype_id = $dbRow['addresstype_id'];
}

if ($contacttype_id == "")
{
    $contacttype_id = "NULL";
}
if ($phonetype_id == "")
{
    $phonetype_id = "NULL";
}
if ($emailtype_id == "")
{
    $emailtype_id = "NULL";
}
if ($addresstype_id == "")
{
    $addresstype_id = "NULL";
}
$contactTypes = contacts::getall_contacttypes();

$contactPhoneTypes = contacts::getall_contacts_phonetypes();
$contactEmailTypes = contacts::getall_contacts_emailtypes();

$contactAddressTypes = contacts::getall_contacts_addresstypes();

                
include("contacts_configure.phtml");

?>
