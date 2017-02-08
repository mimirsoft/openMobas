<?
include("../../../framework/framework_masterinclude.php");
include("tenants_include.php");
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
    case "target":
    $contactSet = contacts::getall_contacts_of_type($VALUE);
    $render = "<addrow>";
    $render .= "<removerow>contacts_id_row</removerow>";
    $render .= "<previousrow>target_row</previousrow>";
    $render .= "<label>Select Contact:</label>";
    $render .= "<rowname>contacts_id_row</rowname>";
    $render .= "<rowformname>contacts_id</rowformname>";
    foreach($contactSet as $row)
    {
        $render .= "<id>".$row['contacts_id']."</id><name>".framework::XML_Replace($row['lastname']);
        if($row['firstname'] != "" )
        {
        $render .= ", ".framework::XML_Replace($row['firstname']);
        }
        $render .= "</name>\n";
    }
    $render .= "</addrow>";
    header('Content-Type: text/xml');
    echo $render;            
} 


?>
