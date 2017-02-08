<?php
include("../../../framework/framework_masterinclude.php");
include("leads_include.php");
Framework::XML_authenticate('Limited');//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$firstname = '';
$lastname = '';
$ssn = '';
$contacttype_id = '';
$street = '';
$city = '';
$state = '';
$zip = '';
$addresstype_id = '';
$careof = '';
$phone_num = '';
$phonetype_id = '';
$contacts_id = 'NULL';

foreach($_POST as $key => $value)
{
    $$key = $value;
}
if ($ACTION == "save_lead" && $lastname != "")
{
    $contacts_id = contact::add_contact($contacts_id, $lastname, $firstname, $ssn, $contacttype_id);
    contact::add_contactaddress("NULL", $street, $city, $state, $zip, $addresstype_id, $contacts_id, $careof);
    contact::add_contactphonenum("NULL", $phone_num, $phonetype_id, $contacts_id);
    header('Content-Type: text/xml');
    echo "<lead>SAVED</lead>";
}
            


?>
