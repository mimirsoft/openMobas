<?php
include("../../../framework/framework_masterinclude.php");
require_once("maintenance_include.php");
Framework::XML_authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication


$ACTION = '';
$VALUE = '';
$render = '';
$SORTBY = '';
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
	case "inventory_id":
	    if($VALUE == 'all' || $VALUE == '')
	    {
            $result = maintenance::getAllOpen($SORTBY);
	    }
		else
		{
		    $result = maintenance::getAllForInventoryID($VALUE, $SORTBY);
		}
		$render = "<results>";
		foreach($result as $row)
		{
			$m_cases = Maintenance::get_maintenance_cases($row['maintenance_id']);
            $status = '';
			foreach($m_cases as $m_case)
            {
                $status .= $m_case['status_text'];
            }
		    $render .= "<row>";
		    $render .= "<id>".$row['maintenance_id']."</id><title>".framework::XML_Replace($row['maintenance_title'])."</title>";
			$render .= "<itemname>".framework::XML_Replace($row['item_name'])."</itemname>";
			$render .= "<vendors>".framework::XML_Replace($row['vendor_string'])."</vendors>";
            $render .= "<status>".framework::XML_Replace($status)."</status>";
			$render .= "<actionneed>".framework::XML_Replace($row['action_needed'])."</actionneed>";
            $render .= "<whoopen>".$row['whoopen_username']."</whoopen>";
            $render .= "<whoclosed>".$row['whoclosed_username']."</whoclosed>";
			$render .= "<whenopen>".$row['whenopen_date']."</whenopen>";
			$render .= "<whenclosed>".$row['whenclosed_date']."</whenclosed>";
            $render .= "<whenfollow>".$row['whenfollowup']."</whenfollow>";
            $render .= "<whenupdate>".$row['whenupdate']."</whenupdate>";
            $render .= "</row>";
			
		}
		$render .= "</results>";
		break;

}
header('Content-Type: text/xml');
echo $render;


?>
