<?
include("../../../framework/framework_masterinclude.php");
require_once("casesystem_include.php");
include("../../../framework/classes/Casesystem.class.php");
Framework::XML_authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication


$ACTION = '';
$VALUE = '';
$render = '';
foreach($_POST as $key => $value)
{
	$$key = $value;
}
foreach($_GET as $key => $value)
{
	$$key = $value;
}
$tag_id_to_name = CV_Main::get_cv_id_to_name_array();    

switch($ACTION)
{
	case "customer_id":
	    if($VALUE == 'none' || $VALUE == '')
	    {
            $result = casesystem::getAllOpenCases();
	    }
		else
		{
		    $result = casesystem::getAllCasesWithCVTag($VALUE);
		}
		
		$render = "<results>";
		foreach($result as $row)
		{
			$render .= "<row>";
		    $render .= "<id>".$row['case_id']."</id><title>".framework::XML_Replace($row['case_title'])."</title>";
			$render .= "<whoopen>".$row['whoopen_username']."</whoopen>";
			$render .= "<status>".framework::XML_Replace($row['status_text'])."</status>";
			$render .= "<whoupdated>".$row['whoupdate_username']."</whoupdated>";
			$render .= "<whoassigned>".$row['whoassign_username']."</whoassigned>";
			$render .= "<whenopen>".$row['whenopen_date']."</whenopen>";
			$render .= "<whenupdated>".$row['whenupdated_date']."</whenupdated>";
			$render .= "<whenfollowup>".$row['whenfollowup']."</whenfollowup>";
			$render .= "<whenclosed>".$row['whenclosed_date']."</whenclosed>";
			$render .= "<whenclosed>".$row['whenclosed_date']."</whenclosed>";
		    $render .= "<tags>";
		    if(@$row['cv_tag_string'] != "")
            {
                $cv_tags = explode(",", $row['cv_tag_string']);
                foreach($cv_tags as $cv_tag)
                {
                    $render .= $tag_id_to_name[$cv_tag]." "; 
                    
                }
            }
			$render .= "</tags>";
            $render .= "</row>";
			
		}
		$render .= "</results>";
		break;

}
header('Content-Type: text/xml');
echo $render;


?>
