<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../../../../openMobas/framework_masterinclude.php");
include("seo_pages_include.php");
include("../../../../openMobas/classes/Seo_page.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "seo_pages_module"))
{
    echo "Permission Denied to access seo pages module.";
    exit;
}
$WARNING['show'] = false;
$seo_page['seo_page_id'] = 'NULL';
$seo_page['seo_page_url'] = '';
$seo_page['seo_page_content'] = '';
$seo_page['seo_page_title'] = '';
$seo_page['meta_description'] = '';
$seo_page['meta_keywords'] = '';
$ACTION = '';

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$seopage = $_POST['seopage'];
    @$seo_page_edit = $_POST['seo_page_edit'];
}


switch($ACTION)
{
    case "Save":
        $seo_new = new Seo_Page($seopage['seo_page_id'], $dbh, $USER, $rbac_user);
        $seo_new->setSeoPageURL($seopage['seo_page_url']);
        $seo_new->setSeoPageTitle($seopage['seo_page_title']);
        $seo_new->setSeoPageContent($seopage['seo_page_content']);
        $seo_new->setMetaDescription($seopage['meta_description']);
        $seo_new->setMetaKeywords($seopage['meta_keywords']);
        $seo_new->save();
    break;
    case "Delete":
        $seo_new = new Seo_Page($seo_page_edit, $dbh, $USER, $rbac_user);
        $seo_new->delete();
    break;
    case "Edit":
        $seo_new = new Seo_Page($seo_page_edit, $dbh, $USER, $rbac_user);
        $seo_page['seo_page_id'] = $seo_page_edit;
        $seo_page['seo_page_url'] = $seo_new->getSeoPageURL();
        $seo_page['seo_page_title'] = $seo_new->getSeoPageTitle();
        $seo_page['seo_page_content'] = $seo_new->getSeoPageContent();
        $seo_page['meta_description'] = $seo_new->getMetaDescription();
        $seo_page['meta_keywords'] = $seo_new->getMetaKeywords();
    break;
    default:
    	
   	break;
}

//Get all entries
$seo = new Seo_Page("", $dbh, $USER, $rbac_user);
$seo_data = $seo->getAllSeoPages();        

include("seo_pages_main.phtml");

?>

