<?
include("../../../framework/framework_masterinclude.php");
include("statements_include.php");
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
    case "addressed":
        switch($VALUE)
        {
            case "Y":
                $result = contacts::getall_contacttypes();
                $render = "<addrow>";
                $render .= "<previousrow>addressed_row</previousrow>";
                $render .= "<label>Contact Type:</label>";
                $render .= "<rowname>contacttype_row</rowname>";
                $render .= "<rowformname>contacttype</rowformname>";
                $render .= "<id>none</id><name> </name>";
                foreach($result as $row)
                {
                    $render .= "<id>".$row['contacttype_id']."</id><name>".$row['contacttype_name']."</name>";
                }
                $render .= "</addrow>";
                break;
            case "N":
                $render = "<removerow>contacttype_row</removerow>";
                break;
        }
    break;
    case "contacttype":
        $result = contacts::getall_contacts_of_type($VALUE);
        $render = "<addrow>";
        $render .= "<removerow>contacts_row</removerow>";
        $render .= "<previousrow>contacttype_row</previousrow>";
        $render .= "<label>Contact:</label>";
        $render .= "<rowname>contacts_row</rowname>";
        $render .= "<rowformname>contacts</rowformname>";
        $render .= "<id>NULL</id><name>  </name>";
        foreach($result as $row)
        {
            $render .= "<id>".$row['contacts_id']."</id><name>".framework::XML_Replace($row['lastname']).", ".framework::XML_Replace($row['firstname'])."</name>";
        }
        $render .= "</addrow>";
        break;
    case "contacts":
        if($VALUE == "NULL")
        {   
            $render = "<addrow>";
            $render .= "<removerow>contact_address_row</removerow>";
            $render .= "<previousrow>contacts_row</previousrow>";
            $render .= "</addrow>";
            break;
        }
        $result = contacts::getall_addresses_from_contact_id($VALUE);
        $render = "<addrow>";
        $render .= "<removerow>contact_address_row</removerow>";
        $render .= "<previousrow>contacts_row</previousrow>";
        $render .= "<label>Address:</label>";
        $render .= "<rowname>contact_address_row</rowname>";
        $render .= "<rowformname>address_id</rowformname>";
        foreach($result as $row)
        {
            $render .= "<id>".$row['address_id']."</id><name>".framework::XML_Replace($row['careof'])." ".$row['street']." ".$row['city']." ".$row['state'].", ".$row['zip']."</name>";
        }
        $render .= "</addrow>";
        break;
}
header('Content-Type: text/xml');
echo $render;            


?>
