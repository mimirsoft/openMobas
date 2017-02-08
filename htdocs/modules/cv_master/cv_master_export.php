<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once("../../../framework/framework_masterinclude.php");
require_once("cv_master_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "cv_master_module"))
{
    echo "Permission access Customer/Vendor Module";
    exit;
}

$cat = CV_Category::getCvCatByName('Owner');  
$customers = CV_Main::search_cv('', $cat['cv_category_id']);
    
include("cv_master_export.phtml");

?>
