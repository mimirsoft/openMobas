<?
include("../../../framework/framework_masterinclude.php");
include("recurring_include.php");
Framework::authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication

$contacttype_id =  0 ;
$EDIT = '';
foreach ($_POST as $key => $value)
{
	$$key = $value;
	unset($_POST->{$key});
}

if ($EDIT == "Set Check Type")
{
	recurring::set_recurring_check($recurringcheck_id, $recurringcheck_type, $recurring_id);
}
if ($EDIT == "Add Address")
{
	recurring::add_recurring_check($checkunique_id, $checkunique_name, $checkunique_memo, $checkunique_street, $checkunique_city, $checkunique_state, $checkunique_zip, $checkunique_careof, $recurringcheck_id);
}
if ($EDIT == "Set Check Address")
{
	recurring::set_check_contact($checkcontact_id, $check_memo, $address_id, $recurringcheck_id);
}

recurring::build_recurringtypearray();
recurring::recurring_address_api($recurring_id);
$contactSet = contacts::getall_contacts_of_type($contacttype_id);
foreach($contactSet as $contact)
{
    $addressSet[$contact['contacts_id']] = contacts::getall_addresses_from_contact_id($contact['contacts_id']);
}
$types = contacts::getall_contacttypes();
$type = contacts::get_contacttype_from_id($contacttype_id);
$accountIDtoFullnameArray = transactions::build_accountIDtoFullName_array(true);



include("recurring_address.phtml");

?>
