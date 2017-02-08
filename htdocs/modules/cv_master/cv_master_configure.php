<?php
require_once("../../../framework_masterinclude.php");
require_once("cv_master_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$WARNING['show'] = false;

$ACTION = '';
$recurringtype_id = '';
$type_name = '';
$onsale_policy = '';
$service_policy = '';
$type_id = "NULL";
$type_info = '';
$type_info['inventorytype_name'] = '';
$type_info['inventorytype_id'] = '';
$type_info['service_policy'] = '';
$type_info['onsale_generate_po'] = '';
$type_info['nativetable'] = '';
$type_info['nativetable_name'] = '';
$type_info['po_expense_account'] = '';
$type_info['sales_revenue_account'] = '';
$type_info['inventory_account'] = '';
$CV_Settings['default_account_payable'] = '';
$CV_Settings['default_account_receivable'] = '';
$cv_category_name = '';

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$type_id = $_POST['type_id'];
    @$inventorytype_edit = $_POST['inventorytype_edit'];
    @$type_name = $_POST['type_name'];
    @$service_policy = $_POST['service_policy'];
    @$onsale_policy = $_POST['onsale_policy'];
    @$nativetable = $_POST['nativetable'];
    @$nativetable_name = $_POST['nativetable_name'];
    @$default_account_receivable = $_POST['default_account_receivable'];
    @$default_account_payable = $_POST['default_account_payable'];
    @$cv_category_name = $_POST['cv_category_name'];
    @$cv_category_id = $_POST['cv_category_id'];
    @$cv_cat_edit = $_POST['cv_cat_edit'];

}

$dbh = new DB_Mysql();
if ($ACTION == "Save Type")
{
    Inventory_Type::create_inventory_type('NULL', $type_name, $service_policy, $onsale_policy, $nativetable, $nativetable_name, $clearing);
}
if ($ACTION == "Edit Type")
{
    $type_info = Inventory_Type::get_inventory_type($inventorytype_edit);
}
if ($ACTION == "Update Type")
{
    Inventory_Type::update_inventory_type($type_id, $type_name, $service_policy, $onsale_policy, $nativetable, $nativetable_name);
}
if ($ACTION == "Update Defaults")
{
    CV_Settings::update_defaults($default_account_receivable, $default_account_payable);
}
if ($ACTION == "Create CV Category")
{
    CV_Category::create_cv_cat($cv_category_name);
}
if ($ACTION == "Update CV Category")
{
    CV_Category::update_cv_cat($cv_category_id, $cv_category_name);
}
if ($ACTION == "Edit CV Category")
{
    $cv_cat = CV_Category::get_cv_cat($cv_cat_edit);
    $cv_category_id = $cv_cat['cv_category_id'];
    $cv_category_name = $cv_cat['cv_category_name'];
    
}

$types=Inventory_Type::getall_inventory_types();

$account_array = transaction::build_account_stack_all();
$CV_Settings =     CV_Settings::get_defaults();
$CV_Categories =     CV_Category::get_all();

include("cv_master_configure.phtml");


?>
