<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("leads_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$WARNING['show'] = false;

$lead_info = '';
$lead_info['firstname'] = '';
$lead_info['lastname'] = '';
$lead_info['phone_num'] = '';
$lead_info['careof'] = '';
$lead_info['street'] = '';
$lead_info['city'] = '';
$lead_info['state'] = '';
$lead_info['zip'] = '';
$lead_info['email_address'] = '';
$ACTION = '';
$VIEW_ALL = 'OPEN'; 
$VIEW_CLOSED = '';
$VIEW_WHOSE = 'OWN';

$SORTBY = 'default';
//$whenreturn_date_year = date('Y');
//$whenreturn_date_month = date('m');
//$whenreturn_date_day = date('d');
//$whenreturn_date_hour = date("h");
//$whenreturn_date_minute = date("i");
$whenreturn_date_year = '';
$whenreturn_date_month = '';
$whenreturn_date_day = '';
$whenreturn_date_hour = '';
$whenreturn_date_minute = '';
$ampm = '';
$search_results = '';
$lead = '';
$string = '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_GET->{$key});
}
if($ampm == "pm")
{
    $whenreturn_date_hour = $whenreturn_date_hour+12;
}


switch($ACTION)
{
    case "Search All":
            if($string != "")
            {
                $search_results = Leads::search($string);
            }
    break;
    case "Add Tag":
        foreach($lead_ids as $lead_id)
        {
            try{
                 lead::add_tag($leadcat_id, $lead_id);
            }
            catch(LeadException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] = $exception->message;
            } 
        }
        $SEARCH_ARRAY = explode(",", $previous_search);
        $VIEW_ALL = $SEARCH_ARRAY[0];
        $SORTBY = $SEARCH_ARRAY[1];
        $VIEW_WHOSE = $SEARCH_ARRAY[2];
        
    break;
        case "Remove Tag":
        leads::delete_tag($leadcat_id, $lead_id);
        $SEARCH_ARRAY = explode(",", $previous_search);
        $VIEW_ALL = $SEARCH_ARRAY[0];
        $SORTBY = $SEARCH_ARRAY[1];
        $VIEW_WHOSE = $SEARCH_ARRAY[2];
    break;
           
    
    case "Remove Tags":
        foreach($lead_ids as $lead_id)
        {
            try{
                lead::delete_tag($leadcat_id, $lead_id);
            }
            catch(CVException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH_ARRAY = explode(",", $previous_search);
        $VIEW_ALL = $SEARCH_ARRAY[0];
        $SORTBY = $SEARCH_ARRAY[1];
        $VIEW_WHOSE = $SEARCH_ARRAY[2];
        
    break;
    case "Set Color":
        foreach($lead_ids as $lead_id)
        {
            try{
                 lead::setColor($lead_id, $color);
            }
            catch(CVException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH_ARRAY = explode(",", $previous_search);
        $VIEW_ALL = $SEARCH_ARRAY[0];
        $SORTBY = $SEARCH_ARRAY[1];
        $VIEW_WHOSE = $SEARCH_ARRAY[2];
        
    break;
    case "Set Closed":
        foreach($lead_ids as $lead_id)
        {
            try{
                 lead::close_lead($lead_id, $USER->GetUserID(), $USER->GetUserID()); 
            }
            catch(CVException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH_ARRAY = explode(",", $previous_search);
        $VIEW_ALL = $SEARCH_ARRAY[0];
        $SORTBY = $SEARCH_ARRAY[1];
        $VIEW_WHOSE = $SEARCH_ARRAY[2];
        
    break;
    case "Generate CSV":
    	header("Pragma: public"); // required
    	header("Expires: 0");
    	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    	header("Cache-Control: private",false); // required for certain browsers
    	header("Content-Type: text/csv");
    	header("Content-Disposition: attachment; filename=\"leads.csv\";" );
    	header("Content-Transfer-Encoding: binary");
    	$out = fopen('php://output', 'w');
    	
    	if(@is_array($lead_ids))
    	{ 
    		foreach($lead_ids as $lead_id)
    		{
    		try{
    			$lead_info = lead::get_lead_from_id($lead_id);
    		}
    		catch(LeadException $exception)
    		{
    			$WARNING['show'] = true;
    			$WARNING['message'] = $exception->message;
    		}
    		$leads2[] = $lead_info;
    		}
    	}
    	
    	
    	//get all our leads
    	//exloped the array to get our target  this is temporary till we implement the check boxing
    	//$SEARCH_ARRAY = explode(",", $previous_search);
    	//$VIEW_ALL = $SEARCH_ARRAY[0];
    	//$SORTBY = $SEARCH_ARRAY[1];
    	//$VIEW_WHOSE = $SEARCH_ARRAY[2];
    	//$leads2 = lead::get_leads($VIEW_ALL, $SORTBY, $VIEW_WHOSE, $USER->GetUserID());
    	if(@is_array($leads2))
    	{ 
    		foreach($leads2 as $row)
        	{
        		fwrite($out, $row['firstname'].",".$row['lastname'].",".$row['street'].",".$row['city'].",".$row['state'].",".$row['zip']."\r\n");
        	}
    	}
    	fclose($out);
    	
    	exit();
    break;
     
    
}

        

//$whenreturn_date = $whenreturn_date_year."-".$whenreturn_date_month."-".$whenreturn_date_day." ".$whenreturn_date_hour.":".$whenreturn_date_minute.":00";
//$whenreturn_date_hour = date("h");

$users = Rbac_User::getAllAllowedTo("access_module", "leads_module", $FRAMEWORK);
$userArray = $FRAMEWORK->getUserArray();		
$pad = array("user_id"=>0, "username"=>"UNASSIGNED");
array_unshift($users, $pad);                       
$userArray[0] = $pad; 

$leadtypes = lead::getall_leadcats();
$leadOrigin = lead::getall_leadorigins();

$settings = lead::getall_settings();
foreach($settings as $row)
{
    $defaults[$row['setting_name']] = $row['setting_value'];
}
$SEARCH = $VIEW_ALL.",".$SORTBY.",".$VIEW_WHOSE;
$leads2 = lead::get_leads($VIEW_ALL, $SORTBY, $VIEW_WHOSE, $USER->GetUserID());
$tag_id_to_name = lead::get_leadtag_to_name_array();

include("leads_main.phtml");
//$whenreturn_date = date('Y-m-d H:i');
//echo $whenreturn_date;
?>
