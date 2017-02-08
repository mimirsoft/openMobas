<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../../../../openMobas/framework_masterinclude.php");
include("seo_pages_include.php");
include("../../../../openMobas/classes/Seo_geoterm.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "seo_pages_module"))
{
    echo "Permission Denied to access seo pages module.";
    exit;
}
$WARNING['show'] = false;
$seo_geo['seo_geoterm_id'] = 'NULL';
$seo_geo['seo_geoterm'] = '';
$seo_geo['seo_geoterm_url'] = '';
$seo_geo['seo_geoterm_title'] = '';
$seo_geo['seo_geoterm_content'] = '';
$ACTION = '';

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$seogeo = $_POST['seogeo'];
    @$seo_geoterm_edit = $_POST['seo_geoterm_edit'];
}


switch($ACTION)
{
    case "Save Geo Term":
        $seo_new = new Seo_Geoterm($seogeo['seo_geoterm_id']);
        $seo_new->setSeoGeoterm($seogeo['seo_geoterm']);
        $seo_new->setSeoGeotermURL($seogeo['seo_geoterm_url']);
        $seo_new->setSeoGeotermTitle($seogeo['seo_geoterm_title']);
        $seo_new->setSeoGeotermContent($seogeo['seo_geoterm_content']);
        $seo_new->save();
    break;
    case "Delete":
        $seo_new = new Seo_Geoterm($seo_geoterm_edit);
        $seo_new->delete();
    break;
    case "EditGeo":
        $seo_new = new Seo_Geoterm($seo_geoterm_edit);
        $seo_geo['seo_geoterm_id'] = $seo_geoterm_edit;
        $seo_geo['seo_geoterm'] = $seo_new->getSeoGeoterm();
        $seo_geo['seo_geoterm_url'] = $seo_new->getSeoGeotermURL();
        $seo_geo['seo_geoterm_title'] = $seo_new->getSeoGeotermTitle();
        $seo_geo['seo_geoterm_content'] = $seo_new->getSeoGeotermContent();
    break;
    default:
    	
   	break;
}

//Get all entries
$seo = new Seo_Geoterm();
try{
$seo_data = $seo->getAllSeoGeoterms();        
}
catch(MysqlException $exception)
{
	echo $exception->message;
}


include("seo_pages_maker.phtml");

?>

