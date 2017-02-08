<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../../../../openMobas/framework_masterinclude.php");
include("seo_pages_include.php");
include("../../../../openMobas/classes/Seo_serviceterm.class.php");
include("../../../../openMobas/classes/Seo_geoterm.class.php");
include("../../../../openMobas/classes/Seo_page.class.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "seo_pages_module"))
{
    echo "Permission Denied to access seo pages module.";
    exit;
}
$WARNING['show'] = false;
$ACTION = '';

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$seo_page_edit = $_POST['seo_page_edit'];
    @$serviceterms =  $_POST['serviceterms'];
    @$geoterms =  $_POST['geoterms'];
    @$page_title =  $_POST['page_title'];
    
}


switch($ACTION)
{
    case "Generate Pages":
        foreach($serviceterms as $serviceterm_id)
        {
            foreach($geoterms as $geoterm_id)
            {
                
                $seo_geoterm = new Seo_Geoterm($geoterm_id);
                $seo_serviceterm = new Seo_Serviceterm($serviceterm_id);
        
                $seo_new = new Seo_Page('NULL');
                $URL = "/".$seo_geoterm->getSeoGeotermURL()."_".$seo_serviceterm->getSeoServicetermURL();
                $seo_new->setSeoPageURL($URL);
                $TITLE = $page_title." ".$seo_geoterm->getSeoGeotermTitle()." ".$seo_serviceterm->getSeoServicetermTitle();
                $seo_new->setSeoPageTitle($TITLE);
                $content_geo = $seo_geoterm->getSeoGeotermContent();
                $content_geo = str_replace("::service::", $seo_serviceterm->getSeoServiceterm(), $content_geo);
                $content_service = $seo_serviceterm->getSeoServicetermContent();
                $content = $content_service." ".$content_geo;
                $seo_new->setSeoPageContent($content);
                $seo_new->save();
                
                $seo_new = new Seo_Page('NULL');
                $URL = "/".$seo_serviceterm->getSeoServicetermURL()."_".$seo_geoterm->getSeoGeotermURL();
                $seo_new->setSeoPageURL($URL);
                $TITLE = $page_title." ".$seo_serviceterm->getSeoServicetermTitle()." ".$seo_geoterm->getSeoGeotermTitle();
                $seo_new->setSeoPageTitle($TITLE);
                $seo_new->setSeoPageContent($content);
                $seo_new->save();
                
            }
            
        }
        
        
    break;
    case "Delete":
        $seo_new = new Seo_Page($seo_page_edit);
        $seo_new->delete();
    break;
    default:
    	
   	break;
}

//Get all entries
$seo = new Seo_Serviceterm();
$seo_serviceterms = $seo->getAllSeoServiceterms();        

//Get all entries
$seo = new Seo_Geoterm();
$seo_geoterms = $seo->getAllSeoGeoterms();        


//Get all entries
$seo = new Seo_Page();
$seo_data = $seo->getAllSeoPages();    

include("seo_pages_generate.phtml");

?>

