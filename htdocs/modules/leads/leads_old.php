<?
include("../../../framework/framework_masterinclude.php");
include("leads_include.php");
Framework::authenticate("Limited");//the two includes must be before the authentica, to supply the needed module name for authentication

$lead_info = '';
$lead_info['firstname'] = '';
$lead_info['lastname'] = '';
$lead_info['phone_num'] = '';
$lead_info['careof'] = '';
$lead_info['street'] = '';
$lead_info['city'] = '';
$lead_info['state'] = '';
$lead_info['zip'] = '';

$phonetypes = contacts::getall_contacts_phonetypes();
        
$addresstypes = contacts::getall_contacts_addresstypes();
$contacttypes = contacts::getall_contacttypes();


include("leads_main.phtml");

?>
