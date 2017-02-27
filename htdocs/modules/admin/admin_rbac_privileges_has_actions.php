<?php
/*
* 
* 
*/
/*
 *
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once("../../../../openMobas/framework_masterinclude.php");
include("admin_include.php");



$sortby = "is_singular";
$sortby2 = "privilege_name";
$ACTION = '';
$id = "NULL";
$ACTION_VALUE = "Add!";
$edit_id = "";
$name = '';
$description = ''; 

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$RBAC = new Rbac($dbh, $USER, $rbac_user, $objSession);

if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}
if(isset($_POST['ACTION']))
{
    @$id =  $_POST['id'];
    @$edit_ids =         $_POST['edit_ids'];
        
    $ACTION = $_POST['ACTION'];
}
if(isset($_GET['sortby']))
{
    $sortby = $_GET['sortby'];
    $sortby2 = $_GET['sortby2'];
}
switch ($ACTION)
{
    case "Delete":
        foreach($edit_ids as $edit_id)
        {
            $RBAC->delete_pmtma($edit_id);
        }
        $id = "NULL";
        $name = "";
        $description = "";
        break;
}
$pmtma_count = $RBAC->count_dmtmo();

$pmtma = $RBAC->get_all_privileges_mtm_actions($sortby, $sortby2);

include("admin_rbac_privileges_has_actions.phtml");

