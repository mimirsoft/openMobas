<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include("../../../../openMobas/framework_masterinclude.php");
include("seo_pages_include.php");
include("../../../../openMobas/classes/Seo_serviceterm.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "seo_pages_module"))
{
    echo "Permission Denied to access seo pages module.";
    exit;
}
$WARNING['show'] = false;
$seo_service['seo_serviceterm_id'] = 'NULL';
$seo_service['seo_serviceterm'] = '';
$seo_service['seo_serviceterm_url'] = '';
$seo_service['seo_serviceterm_title'] = '';
$seo_service['seo_serviceterm_content'] = '';
$ACTION = '';

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$seoservice = $_POST['seoservice'];
    @$seo_serviceterm_edit = $_POST['seo_serviceterm_edit'];
}


switch($ACTION)
{
    case "Save Service Term":
        $seo_new = new Seo_Serviceterm($seoservice['seo_serviceterm_id']);
        $seo_new->setSeoServiceterm($seoservice['seo_serviceterm']);
        $seo_new->setSeoServicetermURL($seoservice['seo_serviceterm_url']);
        $seo_new->setSeoServicetermTitle($seoservice['seo_serviceterm_title']);
        $seo_new->setSeoServicetermContent($seoservice['seo_serviceterm_content']);
        $seo_new->save();
    break;
    case "Delete":
        $seo_new = new Seo_Serviceterm($seo_serviceterm_edit);
        $seo_new->delete();
    break;
    case "Edit":
        $seo_new = new Seo_Serviceterm($seo_serviceterm_edit);
        $seo_service['seo_serviceterm_id'] = $seo_serviceterm_edit;
        $seo_service['seo_serviceterm'] = $seo_new->getSeoServiceterm();
        $seo_service['seo_serviceterm_url'] = $seo_new->getSeoServicetermURL();
        $seo_service['seo_serviceterm_title'] = $seo_new->getSeoServicetermTitle();
        $seo_service['seo_serviceterm_content'] = $seo_new->getSeoServicetermContent();
    break;
    default:
    	
   	break;
}

//Get all entries
$seo = new Seo_Serviceterm();
$seo_data = $seo->getAllSeoServiceterms();        

include("seo_pages_maker2.phtml");

?>

