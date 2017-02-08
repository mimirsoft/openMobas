<?php
include("../../../framework/framework_masterinclude.php");
Framework::authenticate();

include_once ("calendar_include.php");

if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "calendar_module")))
{
    echo "PERMISSION DENIED TO ACCESS MODULE CALENDAR!!";
    exit;
}
//Get our calendar_user
$calendar_user = new Calendar_User($USER->GetUserID());
//Get user's settings
$calendar_config = new Calendar_config();
$helper = new Helper();

if ( $calendar_user->getDefaultView() )
{
    $helper->send_to_preferred_view ();
}
elseif ($calendar_config->getDefaultView())
{
    
}
else
{
    $helper->do_redirect ( "month.php" );
}
  
  
  
?>
