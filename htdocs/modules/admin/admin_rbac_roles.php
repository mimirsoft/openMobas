<?php
/*
*/
include("../../../framework_masterinclude.php");
include("admin_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$sortby = "name";
$ACTION = '';
$id = "NULL";
$ACTION_VALUE = "Add!";
$edit_id = "";
$name = '';
$description = ''; 
$importance = '';
$RBAC = new Rbac($dbh, $USER, $rbac_user, $objSession);

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$id = $_POST['id'];
    @$name = $_POST['name'];
    @$description = $_POST['description'];
    @$importance = $_POST['importance'];
    @$edit_id = $_POST['edit_id'];
}    
    
switch ($ACTION)
{
    case "Add!":
        $RBAC->create_roles($id, $name, $description, $importance);
        break;
    case "View / Edit":
        $ACTION_VALUE = "Save!";
        $action = rbac::get_role_by_ID($edit_id);
        $id = $action['role_id'];
        $name = $action['name'];
        $description = $action['description'];
        $importance = $action['importance'];
        break;
    case "Save!":
        $RBAC->update_role($id, $name, $description, $importance);
        $id = "NULL";
        $name = "";
        $description = "";
        break;
    case "Delete":
        $RBAC->delete_role($edit_id);
        $id = "NULL";
        $name = "";
        $description = "";
        break;
}
$roles = $RBAC->get_all_roles($sortby);

include("admin_rbac_roles.phtml");

