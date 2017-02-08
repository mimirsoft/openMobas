<?
include("../../../framework/framework_masterinclude.php");
include("invoices_include.php");
Framework::XML_authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication


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
    case "customer_contact":
        $contactSet = contacts::getall_addresses_from_contact_id($VALUE);
        $render = "<doc>";
        $render .= "<addrow>";
        $render .= "<removerow>shipaddress_id_row</removerow>";
        $render .= "<previousrow>inventory_block</previousrow>";
        $render .= "<label>Ship To Address:</label>";
        $render .= "<rowname>shipaddress_id_row</rowname>";
        $render .= "<rowformname>shipaddress_id</rowformname>";
        foreach($contactSet as $row)
        {
            $render .= "<id>".$row['address_id']."</id><name>".framework::XML_Replace($row['street'])." ".framework::XML_Replace($row['city']).", ".$row['state']." ".$row['zip']."</name>\n";
        }
        $render .= "</addrow>";
        $render .= "<addrow>";
        $render .= "<removerow>billaddress_id_row</removerow>";
        $render .= "<previousrow>inventory_block</previousrow>";
        $render .= "<label>Bill To Address:</label>";
        $render .= "<rowname>billaddress_id_row</rowname>";
        $render .= "<rowformname>billaddress_id</rowformname>";
        foreach($contactSet as $row)
        {
            $render .= "<id>".$row['address_id']."</id><name>".framework::XML_Replace($row['street'])." ".framework::XML_Replace($row['city']).", ".$row['state']." ".$row['zip']."</name>\n";
        }
        $render .= "</addrow>";
        $render .= "</doc>";
        header('Content-Type: text/xml');
        echo $render;            
    break;
    case "use_billing":
        $contactSet = contacts::get_address_from_id($VALUE);
        $render = "<doc>";
        $render .= "<contact_name>".framework::XML_Replace($contactSet['firstname'])." ".framework::XML_Replace($contactSet['lastname']);
        if($contactSet['careof'] != "")
        {
            $render .= "Care Of:".framework::XML_Replace($contactSet['careof']);
        }
        $render .= "</contact_name>";

        $render .= "<address1>".framework::XML_Replace($contactSet['street'])."</address1>";
        $render .= "<address2>".framework::XML_Replace($contactSet['city']).", ".framework::XML_Replace($contactSet['state'])." ".framework::XML_Replace($contactSet['zip'])."</address2>";
        $render .= "<city>".framework::XML_Replace($contactSet['city'])."</city>";
        $render .= "<state>".framework::XML_Replace($contactSet['state'])."</state>";
        $render .= "<zip>".framework::XML_Replace($contactSet['zip'])."</zip>";
        $render .= "</doc>";
        header('Content-Type: text/xml');
        echo $render;            
    break;

} 


?>
