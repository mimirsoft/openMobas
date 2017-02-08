<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("casesystem_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$SORTBY =   'whenupdated_date';
$ACTION =     '';
$VIEW_ALL = 'NO';
$VIEW_CLOSED = 'NO';
$case_title = '';
$casesystem_txt = '';
$hiddencasesystem_txt = '';
$WARNING['show'] = false;

//remove this foreach
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
}
casesystem::checkFollowUp();
if ($ACTION == "Create Case")
{
    if($case_title == '')
    {
        
    }
    else
    {
      $case_id = @casesystem::create_case("NULL", $USER->GetUserID(), $casesystem_main_assigned, $casesystem_txt, $hiddencasesystem_txt, $case_title);
      if($customer_id != '' && $customer_id != 'none')
      {
          casesystem::tagWithCVID($case_id, $customer_id);
      }
    }
}
$users = Rbac_User::getAllAllowedTo("access_module", "casesystem_module");
$userArray = framework::getUserArray();		

//WHich cases do we cant to see?
if($VIEW_CLOSED == 'YES' && $VIEW_ALL == 'YES')
{
    $cases = Casesystem::getAllCases();
}
else if($VIEW_CLOSED == 'YES' && $VIEW_ALL == 'NO')
{
    $cases = Casesystem::getAllCasesAssignedToID($USER->GetUserID());
}
elseif($VIEW_CLOSED == 'NO' && $VIEW_ALL == 'YES')
{
    $cases = Casesystem::getAllOpenCases();
}
else
{
     $cases = Casesystem::getAllOpenCasesAssignedToID($USER->GetUserID());
}
$tag_id_to_name = CV_Main::get_cv_id_to_name_array();    
$customers = CV_Main::getall_customers();

include("casesystem_main.phtml");