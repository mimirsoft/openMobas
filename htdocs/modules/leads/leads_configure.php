<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("leads_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION = '';
$WARNING['show'] = false;
$leadcat_id = 'NULL';
$leadcat_name = '';
$leadorigin_name = '';
$leadorigin_id = 'NULL';
$zip = '';
$zip_name = '';
$zip_id ='';
$user_id ='';
$defaults = array();

foreach ($_POST as $key => $value)
{
    $$key = $value;
}

switch($ACTION)
{
	case "Save Lead Category":
        lead::add_leadcat($leadcat_id, $leadcat_name);
        $leadcat_id = "NULL";
    break;
	case "Save Lead Origin":
        lead::add_leadorigin($leadorigin_id, $leadorigin_name);
        $leadcat_id = "NULL";
    break;
	case "Edit Lead Category":
        $dbRow = lead::get_leadcat_from_id($lead_cat_edit);
        $leadcat_name =    $dbRow['leadcat_name'];
        $leadcat_id =      $dbRow['leadcat_id'];
    break;
	case "Edit Lead Origin":
        $dbRow = lead::get_leadorigin_from_id($lead_origin_edit);
        $leadorigin_name =    $dbRow['leadorigin_name'];
        $leadorigin_id =      $dbRow['leadorigin_id'];
    break;
	case "Save Defaults":
        lead::delete_setting();
        foreach($defaults as $default_name => $default_value)
        {
            lead::save_setting($default_name, $default_value);
        }
        unset($defaults);
    break;
	case "Save Zip":
        try{
            LeadZip::create($zip, $zip_name);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }  
    break;
    case "Update Zip":
        try{
            LeadZip::update($zip_id, $zip, $zip_name);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        } 
    break;
    case "Edit Zip":
        $dbRow = LeadZip::get($zip_id);
        $zip_name =    $dbRow['zip_name'];
        $zip =      $dbRow['zip'];
        $zip_id =      $dbRow['zip_id'];
        
    break;
	break;
	case "Assign Zip":
        try{
            LeadZip::createZipToUser($zip_id, $user_id);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }        
    break;
	case "Delete Assigned":
        LeadZip::deleteAssigned($zip_id, $user_id);
    break;
	case "Delete Zip":
        LeadZip::delete($zip_id);
    break;
    case "Reset Followups":
    	lead::reset_followups();
    break;
    

}
$leadTypes = lead::getall_leadcats();
$settings = lead::getall_settings();
$leadOrigin = lead::getall_leadorigins();
$users = Rbac_User::getAllAllowedTo("access_module", "leads_module", $FRAMEWORK);

$leadZips = LeadZip::getall();
$assignedZips = LeadZip::getAssigned();

foreach($settings as $row)
{
    $defaults[$row['setting_name']] = $row['setting_value'];
}
include("leads_configure.phtml");

?>
