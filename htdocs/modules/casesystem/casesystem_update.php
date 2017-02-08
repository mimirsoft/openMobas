<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("casesystem_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$whenreturn_date_year = date('Y');
$whenreturn_date_month = date('m');
$whenreturn_date_day = date('d');
$whenreturn_date_hour = date("h");
$whenreturn_date_minute = date("i");
$ampm = '';
$callback = '';
$print = false;
$ACTION = '';
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_GET->{$key});
}
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$case_id = $_POST['case_id'];
    @$cv_id = $_POST['cv_id'];
    @$customer_id = $_POST['customer_id'];
}
switch($ACTION)
{
    case "Remove CV Tag":
        Casesystem::deleteCVTag($case_id, $cv_id);
    break;
    case "Add Tag":
     	try{
             Casesystem::tagWithCVID($case_id, $customer_id);
        }
        catch(LeadException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        } 
    break;
}
        
$users = Rbac_User::getAllAllowedTo("access_module", "maintenance_module");
foreach($users as $user)
{
    $userArray[$user['user_id']] = $user['username'];
}

$userArray[0] = "OPEN";

$caseinfo = Casesystem::get_case($case_id);
$caseentrys = Casesystem::getAllEntries($case_id);

$case_tags = Casesystem::getAllCVTags($case_id);
$customers = CV_Main::getall_customers();
if($print)
{
    include("casesystem_print.phtml");
}
else{
    include("casesystem_update.phtml");
}