<?
include("../../../framework/framework_masterinclude.php");
include("contacts_include.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "contacts_module"))
{
    echo "Permission Denied to access contacts module.";
    exit;
}

$type_id = '';
$sort = '';
$view = '';
foreach ($_GET as $key => $value)
{
	$$key = $value;
	unset($_POST->{$key});
}

$dbh = new DB_Mysql();
$type_info = contacts::get_contacttype_from_id($type_id);

$result = contacts::getall_contacts_of_type($type_id);
foreach($result as $row)
{
    $phone = '';
    $phones = contacts::getall_phonenumbers_from_contact_id($row['contacts_id']);
    foreach($phones as $row2)
    {
        $phone .= $row2['phonetype_name']." ".$row2['phone_num']."<BR />";
    }
    $row['phone'] = $phone;
    $addy = '';
    $addresses = contacts::getall_addresses_from_contact_id($row['contacts_id']);
    foreach($addresses as $row2)
    {
        $addy .= $row2['street']." ".$row2['city'].", ".$row2['state'].", ".$row2['zip']."<BR />";
    }
    $row['addresses'] = $addy;
    $contacts[] = $row;
}
    function compare_lastname($x, $y)
    {
        if ( $x["lastname"] == $y["lastname"] )
        return 0;
        else if ( $x["lastname"] < $y["lastname"] )
        return -1;
        else
        return 1;
    }
    function compare_firstname($x, $y)
    {
        if ( $x["firstname"] == $y["firstname"] )
        return 0;
        else if ( $x["firstname"] < $y["firstname"] )
        return -1;
        else
        return 1;
    }
    function compare_phone($x, $y)
    {
        if ( $x["phone"] == $y["phone"] )
        return 0;
        else if ( $x["phone"] < $y["phone"] )
        return -1;
        else
        return 1;
    }
    function compare_address($x, $y)
    {
        if ( $x["addresses"] == $y["addresses"] )
        return 0;
        else if ( $x["addresses"] < $y["addresses"] )
        return -1;
        else
        return 1;
    }
    switch($sort)
    {
        case "lastname":
            usort($contacts, 'compare_lastname');
        break;
        case "firstname":
             usort($contacts, 'compare_firstname');
        break;
        case "phone":
            usort($contacts, 'compare_phone');
        break;
        case "address":
            usort($contacts, 'compare_address');
        break;
        default:
            usort($contacts, 'compare_lastname');
    }
    switch($view)
    {
        case "label":
            include("contacts_print2.phtml");
        break;
        default:
            include("contacts_print.phtml");

    }

?>
